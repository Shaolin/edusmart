<?php

namespace App\Http\Controllers\Teacher;

use DB;
use App\Models\Term;
use App\Models\Result;
use App\Models\School;
use App\Models\Session;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeacherResultController extends Controller
{
    // List students for the teacher
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $classIds = $teacher->formClasses()->pluck('id');

        $query = Student::with('schoolClass')->whereIn('class_id', $classIds);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $students = $query->orderBy('name')->paginate(10);

        return view('teachers.results.index', [
            'students' => $students,
            'totalStudents' => $students->total(),
        ]);
    }
    // Create result
    public function create(Request $request, Student $student)
{
    $sessions = AcademicSession::all();
    $terms = Term::all();

    $sessionId = $request->session_id ?? AcademicSession::where('is_active', 1)->value('id');
    $termId    = $request->term_id ?? Term::where('is_active', 1)->value('id');

    $subjects = $student->schoolClass->subjects;

    return view('teachers.results.create', compact(
        'student', 'sessions', 'terms', 'subjects', 'sessionId', 'termId'
    ));
}

    // Show form for entering a single student's results
    public function edit(Student $student, Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher->formClasses->pluck('id')->contains($student->class_id)) {
            abort(403, 'You are not authorized to enter results for this student.');
        }

        $sessions = AcademicSession::all();
        $terms = Term::all();
        // $sessionId = $request->session ?? $sessions->first()->id;
        // $termId = $request->term ?? $terms->first()->id;
        $sessionId = $request->query('session_id') ?? $sessions->first()->id;
$termId    = $request->query('term_id') ?? $terms->first()->id;

        $subjects = $student->schoolClass->subjects()->get();

        $existingResults = Result::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->get()
            ->keyBy('subject_id');

        $teacherRemark = $existingResults->first()?->teacher_remark ?? '';

        return view('teachers.results.edit', compact(
            'student', 'sessions', 'terms', 'subjects', 
            'existingResults', 'teacherRemark', 'sessionId', 'termId'
        ));
    }

    // Store/update results
    public function update(Request $request, Student $student)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher->formClasses->pluck('id')->contains($student->class_id)) {
            abort(403, 'You are not authorized to update results for this student.');
        }
        

        $data = $request->validate([
            
            'session_id'   => 'required|exists:sessions,id',
            'term_id'      => 'required|exists:terms,id',
            'subject_id.*' => 'required|exists:subjects,id',
            'test_score.*' => 'nullable|numeric|min:0|max:40',
            'exam_score.*' => 'nullable|numeric|min:0|max:60',
            'teacher_remark' => 'nullable|string|max:255',
        ]);

        $savedCount = 0;
        $incompleteSubjects = [];

        foreach ($data['subject_id'] as $i => $subjectId) {
            $test = $data['test_score'][$i] ?? null;
            $exam = $data['exam_score'][$i] ?? null;

            if ($test === null || $exam === null || $test === '' || $exam === '') {
                $subject = Subject::find($subjectId);
                $incompleteSubjects[] = $subject ? $subject->name : "Subject #$subjectId";
                continue;
            }

            $total = $test + $exam;
            [$grade, $remark] = $this->computeGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'term_id'    => $data['term_id'],
                    'session_id' => $data['session_id'],
                ],
                [
                    'test_score'     => $test,
                    'exam_score'     => $exam,
                    'total_score'    => $total,
                    'grade'          => $grade,
                    'remark'         => $remark,
                    'teacher_remark' => $data['teacher_remark'] ?? null,
                ]
            );

            $savedCount++;
        }

        $message = "✅ $savedCount subject(s) saved successfully.";
        if (!empty($incompleteSubjects)) {
            $message .= ' ⚠️ Some subjects were not saved: ' . implode(', ', $incompleteSubjects);
        }

        

            return redirect()->route('teachers.results.show', [
                'student'    => $student->id,
                'session_id' => $data['session_id'],
                'term_id'    => $data['term_id'],
            ])->with('success', $message);
   
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

    // Show student results with correct ranking
    public function show(Request $request, Student $student)
    {

    
       
        $sessionId = $request->session_id 
    ?? AcademicSession::where('is_active', 1)->value('id');

$termId = $request->term_id 
    ?? Term::where('is_active', 1)->value('id');
//         $sessionId = $request->session_id ?? $request->session ?? AcademicSession::latest()->first()->id;
// $termId    = $request->term_id ?? $request->term ?? Term::latest()->first()->id;

        $session = AcademicSession::find($sessionId);
        $term    = Term::find($termId);

        

        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->get();

        $school = School::find(Auth::user()->school_id);

        // Fetch all students in the same class
        $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');

        $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
            ->whereIn('student_id', $classStudentIds)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->get();

        // ✅ Correct ranking logic with ties
        $total_students = $classTotals->count();
        $position = null;

        if ($total_students > 0) {
            $rank = 1;
            $prevTotal = null;
            $realRanks = [];

            foreach ($classTotals as $index => $row) {
                if ($prevTotal !== null && $row->total < $prevTotal) {
                    $rank = $index + 1;
                }
                $realRanks[$row->student_id] = $rank;
                $prevTotal = $row->total;
            }

            $position = $realRanks[$student->id] ?? null;
        }

        return view('teachers.results.show', compact(
            'student', 'results', 'session', 'term', 'school', 'position', 'total_students',
            'sessionId', 'termId'
        ));
    }

    // PDF download with correct ranking
    public function download(Student $student, Request $request)
    {
        $teacher = auth()->user()->teacher;
        $formClassIds = $teacher->formClasses()->pluck('id');

        if (! $formClassIds->contains($student->class_id)) {
            abort(403, 'You are not authorized to download results for this student.');
        }

        $sessionId = $request->query('session') ?? $request->query('session_id');
        $termId    = $request->query('term') ?? $request->query('term_id');

        $session = AcademicSession::find($sessionId) ?? AcademicSession::latest()->first();
        $term    = Term::find($termId) ?? Term::latest()->first();

        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('term_id', $term->id)
            ->get();

        $school = School::find(Auth::user()->school_id);

        // Correct ranking
        $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');

        $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
            ->whereIn('student_id', $classStudentIds)
            ->where('session_id', $session->id)
            ->where('term_id', $term->id)
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->get();

        $total_students = $classTotals->count();
        $position = null;

        if ($total_students > 0) {
            $rank = 1;
            $prevTotal = null;
            $realRanks = [];

            foreach ($classTotals as $index => $row) {
                if ($prevTotal !== null && $row->total < $prevTotal) {
                    $rank = $index + 1;
                }
                $realRanks[$row->student_id] = $rank;
                $prevTotal = $row->total;
            }

            $position = $realRanks[$student->id] ?? null;
        }

        $data = compact('student', 'results', 'session', 'term', 'school', 'position', 'total_students');

        try {
            if (! view()->exists('teachers.results.pdf')) {
                abort(500, 'PDF view "teachers.results.pdf" not found.');
            }

            $pdf = Pdf::loadView('teachers.results.pdf', $data);
            $fileName = preg_replace('/\s+/', '_', $student->name) . '_Result.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }
}




<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
            Enter Results — {{ $student->name }} ({{ $student->schoolClass->name ?? '-' }})
        </h2>
    </x-slot>

    <div class="p-6">
        {{-- Success & Error Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('teachers.results.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            {{-- Session & Term --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Session</label>
                    <select name="session_id" class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $sessionId ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Term</label>
                    <select name="term_id" class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ $term->id == $termId ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto mb-6">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Test (40)</th>
                            <th class="px-4 py-3 text-left">Exam (60)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $index => $subject)
                            @php
                                $result = $existingResults[$subject->id] ?? null;
                            @endphp
                            <tr class="{{ $index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                    {{ $subject->name }}
                                    <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="test_score[]" min="0" max="40"
                                           class="w-28 sm:w-36 border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('test_score.' . $index, $result->test_score ?? '') }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="exam_score[]" min="0" max="60"
                                           class="w-28 sm:w-36 border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('exam_score.' . $index, $result->exam_score ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4 mb-6">
                @foreach($subjects as $index => $subject)
                    @php
                        $result = $existingResults[$subject->id] ?? null;
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Subject:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $subject->name }}</span>
                            <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-500 dark:text-gray-400 text-sm">Test (40)</label>
                            <input type="number" name="test_score[]" min="0" max="40"
                                   class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                   value="{{ old('test_score.' . $index, $result->test_score ?? '') }}">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-500 dark:text-gray-400 text-sm">Exam (60)</label>
                            <input type="number" name="exam_score[]" min="0" max="60"
                                   class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                   value="{{ old('exam_score.' . $index, $result->exam_score ?? '') }}">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Teacher's Remark --}}
            <div class="mb-6">
                <label class="block font-semibold mb-2">Teacher's Remark</label>
                <textarea name="teacher_remark" rows="4"
                          class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100"
                          placeholder="Enter a general remark...">{{ old('teacher_remark', $teacherRemark) }}</textarea>
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg w-full sm:w-auto">
                Save Results
            </button>
        </form>
    </div>
</x-app-layout>