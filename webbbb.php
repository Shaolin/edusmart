<!-- StudentController -->
<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Result;
use App\Models\School;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Mail\StudentResultMail;
use App\Models\AcademicSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
   
   use App\Mail\ResultMail;
   use App\Services\ResultService;
   use Illuminate\Validation\Rule;

   
   




class StudentController extends Controller
{

    protected $resultService;

public function __construct(ResultService $resultService)
{
    $this->resultService = $resultService;
}

    /**
     * Display a listing of students.
     */
    public function index()
    {
        $user = Auth::user();

        // Base query: students belonging to the admin's school
        $query = Student::with(['schoolClass', 'guardian'])
                        ->where('school_id', $user->school_id);

        // Apply filters from request (name / class_id / gender)
        if ($name = request('name')) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($class_id = request('class_id')) {
            $query->where('class_id', $class_id);
        }
        if ($gender = request('gender')) {
            $query->where('gender', $gender);
        }

        // If logged-in user is a teacher, restrict results to their form class (if any)
        if ($user->role === 'teacher' && $user->teacher) {
            $formClass = $user->teacher->formClasses()->first();
            if ($formClass) {
                $query->where('class_id', $formClass->id);
            } else {
                $students = collect();
                $classes = SchoolClass::where('school_id', $user->school_id)->get();
                return view('students.index', compact('students', 'classes'));
            }
        }

        // Paginate and preserve query string for links
        $students = $query->paginate(10)->appends(request()->query());

        // Classes only for this admin's school
        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('students.index', compact('students', 'classes'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $user = Auth::user();
        $classes = SchoolClass::where('school_id', $user->school_id)->get();
        $guardians = Guardian::where('school_id', $user->school_id)->get();

        return view('students.create', compact('classes', 'guardians'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'gender'           => 'required|in:male,female',
            // 'admission_number' => 'required|unique:students,admission_number',
            'admission_number' => [
    'required',
    Rule::unique('students', 'admission_number')
        ->where(fn ($query) => $query->where('school_id', $user->school_id)),
],


            'class_id'         => 'required|exists:classes,id',
            'guardian_id'      => 'nullable|exists:guardians,id',
            'guardian_name'    => 'nullable|string|max:255',
            'guardian_phone'   => 'nullable|string|max:20',
        ]);

        // Handle guardian creation if not provided
        if (!empty($validated['guardian_id'])) {
            $guardianId = $validated['guardian_id'];
        } else {
            $request->validate([
                'guardian_name'  => 'required|string|max:255',
                'guardian_phone' => 'required|string|max:20|unique:guardians,phone',
            ]);

            $guardian = Guardian::create([
                'name'      => $request->guardian_name,
                'phone'     => $request->guardian_phone,
                'school_id' => $user->school_id, // assign school_id to guardian
            ]);

            $guardianId = $guardian->id;
        }

        // Create student and assign school_id
        Student::create([
            'name'             => $validated['name'],
            'gender'           => $validated['gender'],
            'admission_number' => $validated['admission_number'],
            'class_id'         => $validated['class_id'],
            'guardian_id'      => $guardianId,
            'school_id'        => $user->school_id, // assign school_id to student
        ]);

        return redirect()->route('students.index')
                         ->with('success', 'Student added successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['schoolClass', 'guardian']);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $user = Auth::user();
        $classes = SchoolClass::where('school_id', $user->school_id)->get();
        $guardians = Guardian::where('school_id', $user->school_id)->get();

        return view('students.edit', compact('student', 'classes', 'guardians'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'gender'           => 'required|in:male,female',
            // 'admission_number' => 'required|unique:students,admission_number,' . $student->id,

            'admission_number' => [
            'required',
            Rule::unique('students', 'admission_number')
                ->ignore($student->id)
                ->where(fn ($query) =>
                    $query->where('school_id', $user->school_id)
                ),
        ],

            'class_id'         => 'required|exists:classes,id',
            'guardian_id'      => 'nullable|exists:guardians,id',
            'guardian_name'    => 'nullable|string|max:255',
            'guardian_phone'   => 'nullable|string|max:20',
        ]);

        if (!empty($validated['guardian_id'])) {
            $guardianId = $validated['guardian_id'];
        } else {
            $request->validate([
                'guardian_name'  => 'required|string|max:255',
                'guardian_phone' => 'required|string|max:20|unique:guardians,phone,' . ($student->guardian_id ?? null),
            ]);

            $guardian = Guardian::create([
                'name'      => $request->guardian_name,
                'phone'     => $request->guardian_phone,
                'school_id' => $user->school_id,
            ]);

            $guardianId = $guardian->id;
        }

        $student->update([
            'name'             => $validated['name'],
            'gender'           => $validated['gender'],
            'admission_number' => $validated['admission_number'],
            'class_id'         => $validated['class_id'],
            'guardian_id'      => $guardianId,
            'school_id'        => $user->school_id, // ensure school_id stays consistent
        ]);

        return redirect()->route('students.index')
                         ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Student deleted successfully.');
    }

    /**
     * Promote students to next class (bulk action).
     */
    public function promote(Request $request)
    {
        $validated = $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'next_class_id' => 'required|exists:classes,id',
        ]);

        Student::whereIn('id', $validated['student_ids'])
            ->update(['class_id' => $validated['next_class_id']]);

        return redirect()->route('students.index')
                         ->with('success', 'Students promoted successfully.');
    }

   // send result
   
   
  

   
   
   
   public function sendResult($studentId)
   {
       $student = Student::with(['school', 'schoolClass'])->findOrFail($studentId);
       $results = $student->results; // ensure this relationship exists
       $school = $student->school;
   
       // Send to the actual parent's email
       Mail::raw('Testing Gmail SMTP from Laravel', function ($msg) {
        $msg->to('agozieokolo2@gmail.com')->subject('Gmail Test');

        
    });
    
   
       return back()->with('success', 'Result sent successfully!');
   }
   
   

//    pdf result generator
public function generatePdf($studentId)
{
    $student = Student::with(['school', 'schoolClass', 'guardian', 'results.subject'])->findOrFail($studentId);
    $term = Term::find(request('term_id'));
    $session = AcademicSession::find(request('session_id'));
    $school = $student->school;
    $results = $student->results;
    $position = request('position');
    $total_students = request('total_students');

    $pdf = Pdf::loadView('results.pdf', compact('student', 'results', 'school', 'term', 'session', 'position', 'total_students'));

    $fileName = 'results/' . $student->id . '.pdf';

    // 🔥 Ensure the folder exists
    Storage::disk('public')->makeDirectory('results');

    // 🔥 Save correctly into public disk
    Storage::disk('public')->put($fileName, $pdf->output());

    return $fileName;
}

   

   


}


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                    Results — {{ $student->name }}
                </h2>
                <p class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ $term->name }} | {{ $session->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto relative" id="result-sheet">
        {{-- Watermark --}}
        @if($school && $school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}"
                 class="absolute top-1/2 left-1/2 w-48 sm:w-72 opacity-5 -translate-x-1/2 -translate-y-1/2 rotate-12 z-0">
        @endif

        {{-- School Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 relative z-10">
            <div class="flex items-center gap-3">
                @if($school && $school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                @endif
                <div class="text-sm sm:text-base text-gray-800 dark:text-gray-100">
                    <h1 class="font-bold text-blue-800 dark:text-blue-400 text-lg sm:text-xl">{{ $school->name ?? 'School Name' }}</h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ $school->address ?? '' }}</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Contact: <span class="text-blue-600 dark:text-blue-400">{{ $school->phone ?? 'Not set' }}</span>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-700 rounded font-semibold text-xs sm:text-sm text-yellow-800 dark:text-yellow-100">
                    Result Sheet
                </span>
            </div>
        </div>

        {{-- Student Info Card --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 relative z-10 text-gray-800 dark:text-gray-100">
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Student Info</h3>
                <p><strong>Name:</strong> {{ $student->name }}</p>
                <p><strong>Admission No:</strong> {{ $student->admission_number ?? '—' }}</p>
                <p><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</p>
            </div>
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Session & Term</h3>
                <p><strong>Term:</strong> {{ $term->name }}</p>
                <p><strong>Session:</strong> {{ $session->name }}</p>
                <p><strong>Date:</strong> {{ now()->format('d M, Y') }}</p>
            </div>
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Summary</h3>
                @php
                    $totalScore = $results->sum('total_score');
                    $subjectCount = $results->count();
                    $average = $subjectCount ? number_format($totalScore / $subjectCount, 2) : '0.00';
                @endphp
                <p><strong>Total Score:</strong> {{ $totalScore }}</p>
                <p><strong>Average:</strong> {{ $average }}</p>
                <p><strong>Position:</strong> {{ $position ?? '—' }} out of {{ $total_students ?? '—' }}</p>
                <div class="text-sm text-gray-700 dark:text-gray-200">
                   
                </div>
            </div>
        </div>

        {{-- Results Table --}}
        <div class="overflow-x-auto relative z-10 rounded-lg mb-6">
            <table class="min-w-full border border-gray-300 dark:border-gray-700 text-xs sm:text-sm">
                <thead class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100">
                    <tr>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-left">Subject</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Test (40)</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Exam (60)</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Total</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Grade</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Remark</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 dark:text-gray-100">
                    @foreach($results as $result)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-indigo-600 dark:text-indigo-400">{{ $result->subject->name }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->test_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->exam_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->total_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->grade }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Teacher Remark --}}
        @if($results->first() && $results->first()->teacher_remark)
            <div class="mt-5 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                <strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('teachers.results.edit', $student->id) }}?session={{ request('session') }}&term={{ request('term') }}"
               class="inline-block px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
                Edit Results
            </a>
            {{-- <a href="{{ route('teachers.results.report', [$student->id, 'session' => request('session'), 'term' => request('term')]) }}"
               class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                View Report
            </a> --}}
            <a href="{{ route('teachers.results.download', [$student->id, 'session' => request('session'), 'term' => request('term')]) }}"
               class="inline-block px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded">
                Download PDF
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden !important; }
            #result-sheet, #result-sheet * { visibility: visible !important; }
            #result-sheet { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
</x-app-layout>
