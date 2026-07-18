<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Result;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\Session;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResultAttendanceSummary;

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

        $attendanceSummary = ResultAttendanceSummary::where([
    'student_id' => $student->id,
    'session_id' => $sessionId,
    'term_id'    => $termId,
])->first();

        return view('teachers.results.edit', compact(
            'student', 'sessions', 'terms', 'subjects', 
            'existingResults', 'teacherRemark', 'sessionId', 'termId', 'attendanceSummary'
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
            'school_opened' => 'nullable|integer|min:0',
'times_present' => 'nullable|integer|min:0',
'times_absent'  => 'nullable|integer|min:0',
        ]);

        $savedCount = 0;
        $incompleteSubjects = [];

        foreach ($data['subject_id'] as $i => $subjectId) {
            $test = $data['test_score'][$i] ?? null;
            $exam = $data['exam_score'][$i] ?? null;

            
            
            if (($test === null || $test === '') && ($exam === null || $exam === '')) {
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

        ResultAttendanceSummary::updateOrCreate(
    [
        'student_id' => $student->id,
        'session_id' => $data['session_id'],
        'term_id'    => $data['term_id'],
    ],
    [
        'school_id'     => auth()->user()->school_id,
        'school_opened' => $request->school_opened,
        'times_present' => $request->times_present,
        'times_absent'  => $request->times_absent,
    ]
);

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
        $sessionId = $request->session_id ?? AcademicSession::where('is_active', 1)->value('id');
        $termId = $request->term_id ?? Term::where('is_active', 1)->value('id');
    
        $session = AcademicSession::find($sessionId);
        $term    = Term::find($termId);
       
    
        $results = Result::with('subject')
            ->where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->get();

            $attendanceSummary = ResultAttendanceSummary::where([
    'student_id' => $student->id,
    'session_id' => $sessionId,
    'term_id'    => $termId,
])->first();
    
        $school = School::find(Auth::user()->school_id);
         $setting = $school->setting;

         // load next term
       $nextTerm = match ($term->name) {
    'First Term'  => 'second',
    'Second Term' => 'third',
    'Third Term'  => 'first',
    default => null,
};

 // Determine the session to use for the fee
$currentSession = $session->name; // e.g. 2025/2026
$feeSession = $currentSession;

if ($term->name === 'Third Term') {

    [$startYear, $endYear] = explode('/', $currentSession);

    $feeSession = ($startYear + 1) . '/' . ($endYear + 1);
}

 // Load next term fee
    $nextTermFee = Fee::where('school_id', $school->id)
        ->where('class_id', $student->class_id)
        ->where('term', $nextTerm)
        ->where('session', $feeSession)
        ->first();
    
        // Fetch all students in the same class
        $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');

    
        $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
            ->whereIn('student_id', $classStudentIds)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->get();
    
        // ✅ Tie-aware ranking
        $rank = 0;
        $prevTotal = null;
        $skip = 0;
        $realRanks = [];
    
        foreach ($classTotals as $row) {
            if ($prevTotal === $row->total) {
                $skip++;
            } else {
                $rank += 1 + $skip;
                $skip = 0;
            }
            $realRanks[$row->student_id] = $rank;
            $prevTotal = $row->total;
        }
    
        $position = $realRanks[$student->id] ?? null;
        $total_students = $classTotals->count();
    
        return view('teachers.results.show', compact(
            'student', 'results', 'session', 'term', 'school', 'position', 'total_students',
            'sessionId', 'termId', 'setting', 'nextTermFee', 'attendanceSummary'
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

    $setting = $school->setting;
// Attendance Summary
    $attendanceSummary = ResultAttendanceSummary::where([
    'student_id' => $student->id,
    'session_id' => $session->id,
    'term_id'    => $term->id,
])->first();

// Load next term
$nextTerm = match ($term->name) {
    'First Term'  => 'second',
    'Second Term' => 'third',
    'Third Term'  => 'first',
    default       => null,
};

// Determine the session to use for the fee
$currentSession = $session->name; // e.g. 2025/2026
$feeSession = $currentSession;

if ($term->name === 'Third Term') {
    [$startYear, $endYear] = explode('/', $currentSession);

    $feeSession = ($startYear + 1) . '/' . ($endYear + 1);
}

// Load next term fee
$nextTermFee = Fee::where('school_id', $school->id)
    ->where('class_id', $student->class_id)
    ->where('term', $nextTerm)
    ->where('session', $feeSession)
    ->first();

    // Fetch all students in the same class
    $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');

    $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
        ->whereIn('student_id', $classStudentIds)
        ->where('session_id', $session->id)
        ->where('term_id', $term->id)
        ->groupBy('student_id')
        ->orderByDesc('total')
        ->get();

    // ✅ Tie-aware ranking
    $rank = 0;
    $prevTotal = null;
    $skip = 0;
    $realRanks = [];

    foreach ($classTotals as $row) {
        if ($prevTotal === $row->total) {
            $skip++;
        } else {
            $rank += 1 + $skip;
            $skip = 0;
        }
        $realRanks[$row->student_id] = $rank;
        $prevTotal = $row->total;
    }

    $position = $realRanks[$student->id] ?? null;
    $total_students = $classTotals->count();
  

    $data = compact('student', 'results', 'session', 'term', 'school', 'position', 'total_students', 'nextTermFee', 'attendanceSummary', 'setting');

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

      // Load annual results
    public function annualResult($student_id, $session_id)
{
    // Load student
    $student = Student::with('schoolClass')->findOrFail($student_id);

    // Load academic session
    $session = AcademicSession::findOrFail($session_id);

    // Load school
    $school = School::find(Auth::user()->school_id);

    // Load school settings
    $setting = $school->setting;

    // Fetch all results for this student in this session
$results = Result::with(['subject', 'term'])
    ->where('student_id', $student->id)
    ->where('session_id', $session->id)
    ->orderBy('subject_id')
    ->get();




    // Fetch all results for this student in this session

   $cumulativeResults = $results->groupBy('subject_id')->map(function ($subjectResults) {

    $first  = $subjectResults->firstWhere('term.name', 'First Term');
    $second = $subjectResults->firstWhere('term.name', 'Second Term');
    $third  = $subjectResults->firstWhere('term.name', 'Third Term');

    $firstScore  = $first?->total_score ?? 0;
    $secondScore = $second?->total_score ?? 0;
    $thirdScore  = $third?->total_score ?? 0;

    $total = $firstScore + $secondScore + $thirdScore;

  // Count how many terms actually have results
$termsAttended = 0;

if ($first) {
    $termsAttended++;
}

if ($second) {
    $termsAttended++;
}

if ($third) {
    $termsAttended++;
}

$average = $termsAttended > 0
    ? round($total / $termsAttended, 2)
    : 0;

    
    $grade = '';
$remark = '';
[$grade, $remark] = $this->computeGrade($average);

return (object)[
    'subject' => $subjectResults->first()->subject,
    'first'   => $firstScore,
    'second'  => $secondScore,
    'third'   => $thirdScore,
    'total'   => $total,
    'average' => $average,
    'grade'   => $grade,
    'remark'  => $remark,
];
});

$annualTotal = $cumulativeResults->sum('total');

$subjectCount = $cumulativeResults->count();

$annualAverage = $subjectCount > 0
    ? round($cumulativeResults->avg('average'), 2)
    : 0;

    //promoted

    $annualAverage = $subjectCount > 0
    ? round($cumulativeResults->avg('average'), 2)
    : 0;

$promotionStatus = $annualAverage >= 40
    ? 'Promoted'
    : 'Not Promoted';

    // Get all students in the same class
$classStudentIds = Student::where('class_id', $student->class_id)
    ->pluck('id');

// Calculate annual total for each student
$classTotals = Result::select(
        'student_id',
        DB::raw('SUM(total_score) as annual_total')
    )
    ->whereIn('student_id', $classStudentIds)
    ->where('session_id', $session->id)
    ->groupBy('student_id')
    ->orderByDesc('annual_total')
    ->get();

    $rank = 0;
$prevTotal = null;
$skip = 0;
$realRanks = [];

foreach ($classTotals as $row) {

    if ($prevTotal === $row->annual_total) {
        $skip++;
    } else {
        $rank += 1 + $skip;
        $skip = 0;
    }

    $realRanks[$row->student_id] = $rank;

    $prevTotal = $row->annual_total;
}

$annualPosition = $realRanks[$student->id] ?? null;

$totalStudents = $classTotals->count();

    

return view('teachers.results.annual_result', compact(
    'student',
    'session',
    'school',
    'setting',
    'cumulativeResults',
    'annualTotal',
    'annualAverage',
    'annualPosition',
    'totalStudents',
     'promotionStatus'
));
}


public function downloadAnnualResult($student_id, $session_id)
{
    // Copy EVERYTHING from annualResult()
    // Load student
    $student = Student::with('schoolClass')->findOrFail($student_id);

    // Load academic session
    $session = AcademicSession::findOrFail($session_id);

    // Load school
    $school = School::find(Auth::user()->school_id);

    // Load school settings
    $setting = $school->setting;

    // Fetch all results for this student in this session
$results = Result::with(['subject', 'term'])
    ->where('student_id', $student->id)
    ->where('session_id', $session->id)
    ->orderBy('subject_id')
    ->get();




    // Fetch all results for this student in this session

   $cumulativeResults = $results->groupBy('subject_id')->map(function ($subjectResults) {

    $first  = $subjectResults->firstWhere('term.name', 'First Term');
    $second = $subjectResults->firstWhere('term.name', 'Second Term');
    $third  = $subjectResults->firstWhere('term.name', 'Third Term');

    $firstScore  = $first?->total_score ?? 0;
    $secondScore = $second?->total_score ?? 0;
    $thirdScore  = $third?->total_score ?? 0;

    $total = $firstScore + $secondScore + $thirdScore;

  // Count how many terms actually have results
$termsAttended = 0;

if ($first) {
    $termsAttended++;
}

if ($second) {
    $termsAttended++;
}

if ($third) {
    $termsAttended++;
}

$average = $termsAttended > 0
    ? round($total / $termsAttended, 2)
    : 0;

    
    $grade = '';
$remark = '';
[$grade, $remark] = $this->computeGrade($average);

return (object)[
    'subject' => $subjectResults->first()->subject,
    'first'   => $firstScore,
    'second'  => $secondScore,
    'third'   => $thirdScore,
    'total'   => $total,
    'average' => $average,
    'grade'   => $grade,
    'remark'  => $remark,
];
});

$annualTotal = $cumulativeResults->sum('total');

$subjectCount = $cumulativeResults->count();

$annualAverage = $subjectCount > 0
    ? round($cumulativeResults->avg('average'), 2)
    : 0;

    // Get all students in the same class
$classStudentIds = Student::where('class_id', $student->class_id)
    ->pluck('id');

// Calculate annual total for each student
$classTotals = Result::select(
        'student_id',
        DB::raw('SUM(total_score) as annual_total')
    )
    ->whereIn('student_id', $classStudentIds)
    ->where('session_id', $session->id)
    ->groupBy('student_id')
    ->orderByDesc('annual_total')
    ->get();

    $rank = 0;
$prevTotal = null;
$skip = 0;
$realRanks = [];

foreach ($classTotals as $row) {

    if ($prevTotal === $row->annual_total) {
        $skip++;
    } else {
        $rank += 1 + $skip;
        $skip = 0;
    }

    $realRanks[$row->student_id] = $rank;

    $prevTotal = $row->annual_total;
}

$annualPosition = $realRanks[$student->id] ?? null;

$totalStudents = $classTotals->count();

    

$pdf = Pdf::loadView('teachers.results.annual_pdf', [
    'student'          => $student,
    'session'          => $session,
    'school'           => $school,
    'setting'          => $setting,
    'cumulativeResults'=> $cumulativeResults,
    'annualTotal'      => $annualTotal,
    'annualAverage'    => $annualAverage,
    'annualPosition'   => $annualPosition,
    'totalStudents'    => $totalStudents,
]);

$pdf->setPaper('A4', 'portrait');

return $pdf->download(
    'Annual_Result_'.$student->admission_number.'.pdf'
);
}


}