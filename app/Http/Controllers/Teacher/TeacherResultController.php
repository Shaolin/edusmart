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


class TeacherResultController extends Controller
{
    // List students for the teacher
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
    
        // 1️⃣ Get IDs of classes the teacher manages
        $classIds = $teacher->formClasses()->pluck('id');
    
        // 2️⃣ Fetch students in those classes (query builder)
        $query = Student::with('schoolClass')
                        ->whereIn('class_id', $classIds);
    
        // 3️⃣ Optional search filter
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
    
        // 4️⃣ Paginate
        $students = $query->orderBy('name')->paginate(3);
    
        return view('teachers.results.index', [
            'students' => $students,
            'totalStudents' => $students->total(),
        ]);
    }
    

        // Show form for entering a single student's results
        public function edit(Student $student, Request $request)
        {
            $teacher = auth()->user()->teacher;
        
          
            // Ensure the teacher manages this student's class
    if (!$teacher->formClasses->pluck('id')->contains($student->class_id)) {
        abort(403, 'You are not authorized to enter results for this student.');
    }
        
            $sessions = AcademicSession::all();
            $terms = Term::all();
        
            $sessionId = $request->session ?? $sessions->first()->id;
            $termId = $request->term ?? $terms->first()->id;
        
            // All subjects for the class
            $subjects = $student->schoolClass->subjects()->get();
        
            // Existing results for this session/term
            $existingResults = Result::where('student_id', $student->id)
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->get()
                ->keyBy('subject_id');
        
            // Single teacher remark (from any existing result)
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

    // Ensure the teacher manages this student's class
    if (!$teacher->formClasses->pluck('id')->contains($student->class_id)) {
        abort(403, 'You are not authorized to update results for this student.');
    }

    // Validate the inputs
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

        // Skip subjects with empty inputs
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

    // Final message
    $message = "✅ $savedCount subject(s) saved successfully.";

    if (!empty($incompleteSubjects)) {
        $message .= ' ⚠️ Some subjects were not saved: '
            . implode(', ', $incompleteSubjects);
    }

    // return redirect()->back()->with('success', $message);
    return redirect()
    ->route('teachers.results.show', $student->id)
    ->with([
        'success' => $message,
        'session' => $data['session_id'],
        'term'    => $data['term_id']
    ]);

}

    
    
    
        // Helper method to compute grade
         // Helper method to compute grade
         private function computeGrade($total)
         {
             if ($total >= 70) return ['A', 'Excellent'];
             if ($total >= 60) return ['B', 'Very Good'];
             if ($total >= 50) return ['C', 'Good'];
             if ($total >= 45) return ['D', 'Fair'];
             if ($total >= 40) return ['E', 'Pass'];
             return ['F', 'Fail'];
         }

        // Show result

        public function show(Request $request, Student $student)
        {
            // Selected session & term
            $sessionId = $request->query('session_id') ?? AcademicSession::latest()->first()->id;
            $termId = $request->query('term_id') ?? Term::latest()->first()->id;
        
            $session = AcademicSession::find($sessionId);
            $term = Term::find($termId);
        
            // Student results
            $results = Result::with('subject')
                ->where('student_id', $student->id)
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->get();
        
            // School data
            $school = School::first();
        
            // Fetch all students in same class
            $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');
        
            // Rank by total score
            $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
                ->whereIn('student_id', $classStudentIds)
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->groupBy('student_id')
                ->orderByDesc('total')
                ->get();
        
            $total_students = $classTotals->count();
            $position = null;
        
            if ($total_students > 0) {
                $position = $classTotals->search(function ($row) use ($student) {
                    return $row->student_id == $student->id;
                });
        
                if ($position !== false) {
                    $position += 1; // convert to 1st, 2nd...
                }
            }
        
            return view('teachers.results.show', compact(
                'student',
                'results',
                'session',
                'term',
                'school',
                'position',
                'total_students',
                'sessionId',
                'termId'
            ));
        }
        

        public function report(Student $student, Request $request)
{
    $teacher = auth()->user()->teacher;

    // Ensure the teacher can view this student's result
    $formClassIds = $teacher->formClasses()->pluck('id');
    if (! $formClassIds->contains($student->class_id)) {
        abort(403, 'You are not authorized to view results for this student.');
    }

    // Get session and term from query parameters
    $sessionId = $request->query('session');
    $termId    = $request->query('term');

    // Fetch results for the student
    $results = Result::with('subject')
        ->where('student_id', $student->id)
        ->where('session_id', $sessionId)
        ->where('term_id', $termId)
        ->get();

    // Optional: compute total & position later
    return view('teachers.results.report', compact('student', 'results'));
}


public function download(Student $student, Request $request)
{
    $teacher = auth()->user()->teacher;

    // Authorization check
    $formClassIds = $teacher->formClasses()->pluck('id');
    if (! $formClassIds->contains($student->class_id)) {
        abort(403, 'You are not authorized to download results for this student.');
    }

    $sessionId = $request->query('session') ?? $request->query('session_id');
    $termId    = $request->query('term') ?? $request->query('term_id');

    // Fallbacks
    $session = \App\Models\AcademicSession::find($sessionId) ?? \App\Models\AcademicSession::latest()->first();
    $term    = \App\Models\Term::find($termId) ?? \App\Models\Term::latest()->first();

    $results = Result::with('subject')
        ->where('student_id', $student->id)
        ->where('session_id', $session->id)
        ->where('term_id', $term->id)
        ->get();

    $school = School::first();

    // compute class position (optional)
    $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');
    $classTotals = Result::select('student_id', \DB::raw('SUM(total_score) as total'))
        ->whereIn('student_id', $classStudentIds)
        ->where('session_id', $session->id)
        ->where('term_id', $term->id)
        ->groupBy('student_id')
        ->orderByDesc('total')
        ->get();

    $total_students = $classTotals->count();
    $position = null;
    if ($total_students > 0) {
        $pos = $classTotals->search(fn($r) => $r->student_id == $student->id);
        if ($pos !== false) $position = $pos + 1;
    }

    $data = compact('student', 'results', 'session', 'term', 'school', 'position', 'total_students');

    try {
        // ensure view exists and render to check for view errors early
        if (! view()->exists('teachers.results.pdf')) {
            abort(500, 'PDF view "teachers.results.pdf" not found.');
        }

        $pdf = Pdf::loadView('teachers.results.pdf', $data);
       
        return $pdf->download($student->name . '_Result.pdf');

        $fileName = preg_replace('/\s+/', '_', $student->name) . '_Result.pdf';

        return $pdf->download($fileName);
    } catch (\Exception $e) {
        // Return an informative error so you can debug quickly
        return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
    }
}


        

    }
    

