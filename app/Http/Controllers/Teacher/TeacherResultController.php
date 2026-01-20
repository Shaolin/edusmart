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

    // Show form for entering a single student's results
    public function edit(Student $student, Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher->formClasses->pluck('id')->contains($student->class_id)) {
            abort(403, 'You are not authorized to enter results for this student.');
        }

        $sessions = AcademicSession::all();
        $terms = Term::all();
        $sessionId = $request->session ?? $sessions->first()->id;
        $termId = $request->term ?? $terms->first()->id;

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

        return redirect()
            ->route('teachers.results.show', $student->id)
            ->with([
                'success' => $message,
                'session' => $data['session_id'],
                'term'    => $data['term_id']
            ]);
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
        $sessionId = $request->query('session_id') ?? AcademicSession::latest()->first()->id;
        $termId    = $request->query('term_id') ?? Term::latest()->first()->id;

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
