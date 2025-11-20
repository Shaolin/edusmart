<?php

namespace App\Mail;

use App\Http\Controllers\ResultController;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $results;
    public $schoolName;

    // Pass data to the Mailable
    public function __construct($student, $results, $schoolName)
    {
        $this->student = $student;
        $this->results = $results;
        $this->schoolName = $schoolName;
    }

    // Build the email
    public function build()
    {
        return $this->subject('Your Child’s Academic Result')
                    ->view('emails.student_result');
    }
}


//  send result method

public function sendResult($studentId)
{
    // Fetch student (replace school_id if needed)
    $student = Student::with('guardian', 'school')->findOrFail($studentId);

    // Example result data (for testing)
    $results = [
        'Mathematics' => 85,
        'English' => 90,
        'Physics' => 78,
    ];

    $schoolName = $student->school->name ?? 'Test School';

    // Send email to guardian
    Mail::to($student->guardian->email)
        ->send(new StudentResultMail($student, $results, $schoolName));

    return back()->with('success', 'Result sent successfully! Check Mailtrap inbox.');
}

// ResultController
<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Result;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Only allow admins for certain actions
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }

    public function selectClass()
    {
        $user = Auth::user();

        // Teachers/admin see only their school classes
        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('results.select_class', compact('classes'));
    }

    public function showStudents($class_id)
    {
        $class = SchoolClass::with('students')
            ->where('school_id', Auth::user()->school_id)
            ->findOrFail($class_id);

        return view('results.students', compact('class'));
    }

    public function createResult($student_id)
    {
        $student = Student::with('schoolClass')
            ->whereHas('schoolClass', fn($q) => $q->where('school_id', Auth::user()->school_id))
            ->findOrFail($student_id);

        $subjects = Subject::where('school_id', Auth::user()->school_id)->get();
        $sessions = AcademicSession::all();
        $terms = Term::all();
        $school = School::find(Auth::user()->school_id);

        return view('results.create_result', compact('student', 'subjects', 'sessions', 'terms', 'school'));
    }

    public function storeResult(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'subject_id.*'   => 'required|exists:subjects,id',
            'test_score.*'   => 'nullable|numeric|min:0|max:40',
            'exam_score.*'   => 'nullable|numeric|min:0|max:60',
            'teacher_remark' => 'nullable|string|max:255',
            'term_id'        => 'required|exists:terms,id',
            'session_id'     => 'required|exists:sessions,id',
        ]);

        $teacherRemark = $request->teacher_remark ?? null;
        $incompleteSubjects = [];
        $savedCount = 0;

        foreach ($request->subject_id as $index => $subjectId) {
            $test = $request->test_score[$index] ?? null;
            $exam = $request->exam_score[$index] ?? null;

            if ($test === null || $exam === null || $test === '' || $exam === '') {
                $subject = Subject::find($subjectId);
                $incompleteSubjects[] = $subject ? $subject->name : "Subject #$subjectId";
                continue;
            }

            $total = $test + $exam;
            [$grade, $remark] = $this->computeGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'term_id'    => $request->term_id,
                    'session_id' => $request->session_id,
                ],
                [
                    'test_score'     => $test,
                    'exam_score'     => $exam,
                    'total_score'    => $total,
                    'grade'          => $grade,
                    'remark'         => $remark,
                    'teacher_remark' => $teacherRemark,
                ]
            );

            $savedCount++;
        }

        $message = "✅ {$savedCount} subject(s) saved successfully.";

        if (!empty($incompleteSubjects)) {
            $message .= ' ⚠️ Some subjects were not saved: ' . implode(', ', $incompleteSubjects);
            return redirect()
                ->route('results.view', [
                    'student_id' => $request->student_id,
                    'term_id'    => $request->term_id,
                    'session_id' => $request->session_id
                ])
                ->with('warning', $message);
        }

        return redirect()
            ->route('results.view', [
                'student_id' => $request->student_id,
                'term_id'    => $request->term_id,
                'session_id' => $request->session_id
            ])
            ->with('success', $message);
    }

    public function update(Request $request, $studentId)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'session_id'     => 'required|exists:sessions,id',
            'term_id'        => 'required|exists:terms,id',
            'subject_id'     => 'required|array',
            'subject_id.*'   => 'exists:subjects,id',
            'test_score'     => 'required|array',
            'exam_score'     => 'required|array',
            'teacher_remark' => 'nullable|string|max:255',
        ]);

        $student = Student::findOrFail($studentId);
        $termId = $request->term_id;
        $sessionId = $request->session_id;
        $teacherRemark = $request->teacher_remark ?? null;
        $updatedCount = 0;

        foreach ($request->subject_id as $index => $subjectId) {
            $test = $request->test_score[$index] ?? null;
            $exam = $request->exam_score[$index] ?? null;

            if ($test === null && $exam === null) continue;

            $total = ($test ?? 0) + ($exam ?? 0);
            [$grade, $remark] = $this->computeGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'term_id'    => $termId,
                    'session_id' => $sessionId,
                ],
                [
                    'test_score'     => $test,
                    'exam_score'     => $exam,
                    'total_score'    => $total,
                    'grade'          => $grade,
                    'remark'         => $remark,
                    'teacher_remark' => $teacherRemark,
                ]
            );

            $updatedCount++;
        }

        if ($updatedCount > 0) {
            return redirect()
                ->route('results.view', [
                    'student_id' => $student->id,
                    'term_id'    => $termId,
                    'session_id' => $sessionId
                ])
                ->with('success', "✅ $updatedCount subject(s) updated successfully.");
        }

        return back()->with('error', '⚠️ No valid scores were entered for update.');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $sessions = AcademicSession::all();
        $terms = Term::all();
        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        $query = Result::query()->with(['student', 'subject', 'term', 'session'])
            ->whereHas('student', fn($q) => $q->where('school_id', $user->school_id));

        if ($request->filled('session_id')) $query->where('session_id', $request->session_id);
        if ($request->filled('term_id')) $query->where('term_id', $request->term_id);
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $results = $query->orderBy('student_id')->get();

        return view('results.index', compact('results', 'sessions', 'terms', 'classes'));
    }

    public function editAll($student_id, $term_id, $session_id)
    {
        $student = Student::with('schoolClass')->findOrFail($student_id);
        $subjects = Subject::where('school_id', Auth::user()->school_id)->get();
        $results = Result::where('student_id', $student_id)
            ->where('term_id', $term_id)
            ->where('session_id', $session_id)
            ->get();
        $term = Term::findOrFail($term_id);
        $session = AcademicSession::findOrFail($session_id);
        $school = School::find(Auth::user()->school_id);

        return view('results.editall', compact('student', 'subjects', 'results', 'term', 'session', 'school'));
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();

        $result = Result::findOrFail($id);
        $result->delete();

        return redirect()
            ->route('results.index')
            ->with('success', '🗑️ Result deleted successfully.');
    }

    private function computeGrade($total)
    {
        if ($total >= 70) return ['A', 'Excellent'];
        if ($total >= 60) return ['B', 'Very Good'];
        if ($total >= 50) return ['C', 'Good'];
        if ($total >= 45) return ['D', 'Fair'];
        if ($total >= 40) return ['E', 'Pass'];
        return ['F', 'Fail'];
    }

    public function view($student_id, $term_id, $session_id)
    {
        $student = Student::with('schoolClass')->findOrFail($student_id);
        $term = Term::findOrFail($term_id);
        $session = AcademicSession::findOrFail($session_id);
        $school = School::find(Auth::user()->school_id);

        $results = Result::where('student_id', $student_id)
            ->where('term_id', $term_id)
            ->where('session_id', $session_id)
            ->with('subject')
            ->get();

        if ($results->isEmpty()) {
            return redirect()->back()->with('warning', '⚠️ No results found for this student.');
        }

        $average = $results->avg('total_score');

        $class_id = $student->schoolClass->id;
        $class_averages = Result::selectRaw('student_id, AVG(total_score) as avg_score')
            ->where('term_id', $term_id)
            ->where('session_id', $session_id)
            ->whereHas('student', fn($q) => $q->where('class_id', $class_id))
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->get();

        $ranked = $class_averages->pluck('student_id')->toArray();
        $position = array_search($student_id, $ranked) + 1;
        $total_students = count($ranked);

        return view('results.generate_result', compact(
            'student', 'term', 'session', 'results', 'average', 'position', 'total_students', 'school'
        ));
    }

    public function generate($student_id, $term_id, $session_id)
    {
        $student = Student::findOrFail($student_id);
        $term = Term::findOrFail($term_id);
        $session = AcademicSession::findOrFail($session_id);
        $results = Result::where('student_id', $student_id)
            ->where('term_id', $term_id)
            ->where('session_id', $session_id)
            ->with('subject')
            ->get();
        $school = School::find(Auth::user()->school_id);

        return view('results.generate_result', compact('student', 'term', 'session', 'results', 'school'));
    }

    public function classRanking($class_id)
    {
        $class = SchoolClass::with('students')
            ->where('school_id', Auth::user()->school_id)
            ->findOrFail($class_id);

        $term_id = request('term_id', Term::latest()->first()->id ?? 1);
        $session_id = request('session_id', AcademicSession::latest()->first()->id ?? 1);

        $students = $class->students->map(function ($student) use ($term_id, $session_id) {
            $results = Result::where('student_id', $student->id)
                ->where('term_id', $term_id)
                ->where('session_id', $session_id)
                ->get();

            $total = $results->sum('total_score');
            $average = $results->count() > 0 ? $total / $results->count() : 0;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'total_score' => $total,
                'average' => $average,
            ];
        })->sortByDesc('average')->values();

        $students = $students->map(fn($student, $index) => array_merge($student, ['position' => $index + 1]));

        return view('results.class_ranking', compact('class', 'students', 'term_id', 'session_id'));
    }
}


// generate_result.blade.php





<form action="{{ route('send.result', $student->id) }}" method="POST">
    @csrf
    <button type="submit" class="px-4 py-2 bg-yellow-400 rounded">Send Result to Parent</button>
</form>


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                    Result — {{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }}
                </h2>
                <p class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ $term->name }} | {{ $session->name }}
                </p>
            </div>

           
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
        <div id="result-sheet" class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-4 sm:p-6 relative font-sans">

            {{-- Watermark --}}
            @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}"
                     class="absolute top-1/2 left-1/2 w-48 sm:w-72 opacity-5 -translate-x-1/2 -translate-y-1/2 rotate-12 z-0">
            @endif

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 relative z-10 mb-6">
                <div class="flex items-center gap-3">
                    @if($school && $school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                    @endif

                    <div class="text-sm sm:text-base text-gray-800 dark:text-gray-100">
                        <h1 class="font-bold text-blue-800 dark:text-blue-400 text-lg sm:text-xl">{{ $school->name ?? 'School Name' }}</h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ $school->address ?? '' }}</p>
                        <p class="text-gray-600 dark:text-gray-300">
                            Contact: <span class="text-blue-600 dark:text-blue-400">{{ $school->phone ?? $school->contact ?? 'Not set' }}</span>
                        </p>
                        @if($school->email)
                            <p class="truncate text-gray-600 dark:text-gray-300">Email: <span class="text-purple-600 dark:text-purple-400">{{ $school->email }}</span></p>
                        @endif
                        @if($school->website)
                            <p class="truncate text-gray-600 dark:text-gray-300">Website: <span class="text-purple-600 dark:text-purple-400">{{ $school->website }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-700 rounded font-semibold text-xs sm:text-sm text-yellow-800 dark:text-yellow-100">
                        Result Sheet
                    </span>
                </div>

            </div>

          



            
            

            {{-- Student Info --}}
            <div class="relative z-10 border-b border-gray-300 dark:border-gray-700 pb-4 mb-6 text-gray-800 dark:text-gray-100">
                <h2 class="font-semibold mb-2 text-sm sm:text-base">Student Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                    <p><strong>Name:</strong> <span class="text-blue-600 dark:text-blue-400">{{ $student->name }}</span></p>
                    <p><strong>Admission No:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $student->admission_number ?? '—' }}</span></p>
                    <p><strong>Class:</strong> <span class="text-green-600 dark:text-green-400">{{ $student->schoolClass->name ?? '—' }}</span></p>
                    <p><strong>Term:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $term->name }}</span></p>
                    <p><strong>Session:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $session->name }}</span></p>
                    <p><strong>Date:</strong> <span class="text-gray-600 dark:text-gray-300">{{ now()->format('d M, Y') }}</span></p>
                </div>
            </div>

            {{-- Results Table --}}
            <div class="overflow-x-auto relative z-10 rounded-lg">
                <table class="min-w-full border border-gray-300 dark:border-gray-700 text-xs sm:text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
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
                        @php $totalSum = 0; $count = 0; @endphp
                        @foreach($results as $result)
                            @php $totalSum += $result->total_score; $count++; @endphp
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

            {{-- Summary --}}
            @if($count > 0)
            <div class="mt-5 text-xs sm:text-sm space-y-1 text-gray-800 dark:text-gray-100">
                <p><strong>Total Score:</strong> {{ $totalSum }}</p>
                <p><strong>Average:</strong> {{ number_format($totalSum / $count, 2) }}</p>
                <p><strong>Position:</strong> {{ $position ?? '—' }} out of {{ $total_students ?? '—' }} students</p>
            </div>
            @endif

            {{-- Teacher Remark --}}
            @if($results->first() && $results->first()->teacher_remark)
                <div class="mt-5 text-xs sm:text-sm text-gray-800 dark:text-gray-100">
                    <h2 class="font-semibold mb-1">Teacher's Remark:</h2>
                    <p class="italic">{{ $results->first()->teacher_remark }}</p>
                </div>
            @endif

            {{-- Edit Button --}}
            @if($results->count() > 0)
            <div class="mt-4 text-center no-print">
                <a href="{{ route('results.editAll', ['student_id'=>$student->id,'term_id'=>$term->id,'session_id'=>$session->id]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm sm:text-base">
                    ✏️ Edit Results
                </a>
            </div>
            @endif

            {{-- Print Button --}}
            <div class="mt-6 text-center no-print">
                <button onclick="window.print()"
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded text-sm sm:text-base">
                    🖨️ Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden !important; }
            #result-sheet, #result-sheet * { visibility: visible !important; }
            #result-sheet { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;
        if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>


// send result method


public function sendResult($studentId)
{
    $student = Student::with(['guardian', 'school'])->findOrFail($studentId);

    $results = $student->results()->get(); // real results from DB
    $schoolName = $student->school->name ?? 'School';

    Mail::to($student->guardian->email)
        ->send(new StudentResultMail($student, $results, $schoolName));

    return back()->with('success', 'Result sent! Check Mailtrap inbox.');
}