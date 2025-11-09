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
