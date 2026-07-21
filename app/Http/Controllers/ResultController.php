<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Result;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // $setting = SchoolSetting::where('school_id', $school->id)->first();
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
            'student', 'term', 'session', 'results', 'average', 'position', 'total_students', 'school', 'setting', 'nextTermFee'
        ));
    }

    public function generateResult($student_id, $term_id = null, $session_id = null)
{
    $student = Student::with('schoolClass')->findOrFail($student_id);
    $school  = School::find(Auth::user()->school_id);

    // Default to latest term/session if not provided
    $term    = $term_id ? Term::find($term_id) : Term::latest()->first();
    $session = $session_id ? AcademicSession::find($session_id) : AcademicSession::latest()->first();

    // Safety: if term/session not found
    if (!$term) $term = new Term(['name' => '—']);
    if (!$session) $session = new AcademicSession(['name' => '—']);

    $results = Result::where('student_id', $student_id)
        ->where('term_id', $term->id)
        ->where('session_id', $session->id)
        ->with('subject')
        ->get();

    // Precompute average
    $average = $results->count() ? $results->avg('total_score') : 0;

    // Precompute class ranking
    $class_id = $student->schoolClass->id ?? null;
    $position = $total_students = null;

    if ($class_id) {
        $class_averages = Result::selectRaw('student_id, AVG(total_score) as avg_score')
            ->where('term_id', $term->id)
            ->where('session_id', $session->id)
            ->whereHas('student', fn($q) => $q->where('class_id', $class_id))
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->get();

        $ranked = $class_averages->pluck('student_id')->toArray();
        $position = $ranked ? array_search($student_id, $ranked) + 1 : null;
        $total_students = count($ranked);
    }

    return view('results.generate_result', compact(
        'student', 'term', 'session', 'results', 'average', 'position', 'total_students', 'school'
    ));
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

      // Annual average
$annualAverage = $subjectCount > 0
    ? round($cumulativeResults->avg('average'), 2)
    : 0;

// Find English and Mathematics annual averages
$english = $cumulativeResults->first(function ($result) {
    return stripos($result->subject->name, 'English Language') !== false
        || stripos($result->subject->name, 'English') !== false
        || stripos($result->subject->name, 'Literacy') !== false;
});

$mathematics = $cumulativeResults->first(function ($result) {
    return stripos($result->subject->name, 'Mathematics') !== false
        || stripos($result->subject->name, 'Math') !== false
        || stripos($result->subject->name, 'Numeracy') !== false;
});

$englishPassed = $english && $english->average >= 40;
$mathPassed    = $mathematics && $mathematics->average >= 40;

// Promotion rule
$promotionStatus = (
    $annualAverage >= 45 &&
    $englishPassed &&
    $mathPassed
)
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

    return view('results.annual_result', compact(
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

// broadsheet

public function broadsheet(Request $request, $class_id)
{
    // Load the selected class and its students
    $class = SchoolClass::with('students')
        ->where('school_id', Auth::user()->school_id)
        ->findOrFail($class_id);

    // Get selected term and session
    $termId = $request->term_id;
    $sessionId = $request->session_id;

    // Load all subjects belonging to this school
  

        $subjects = $class->subjects()
    ->orderBy('name')
    ->distinct()
    ->get();

    // Load all results for the selected class, term and session
    $results = Result::whereIn('student_id', $class->students->pluck('id'))
        ->where('term_id', $termId)
        ->where('session_id', $sessionId)
        ->get()
        ->keyBy(function ($result) {
            return $result->student_id . '_' . $result->subject_id;
        });

        //position

        $positions = [];

foreach ($class->students as $student) {

    $studentResults = $results->filter(function ($result) use ($student) {
        return $result->student_id == $student->id;
    });

    $average = $studentResults->count()
        ? round($studentResults->avg('total_score'), 2)
        : 0;

    $positions[] = [
        'student_id' => $student->id,
        'average'    => $average,
    ];

    
}

usort($positions, function ($a, $b) {
    return $b['average'] <=> $a['average'];
});

$rank = 0;
$skip = 0;
$previousAverage = null;
$classPositions = [];

foreach ($positions as $item) {

    if ($previousAverage === $item['average']) {
        $skip++;
    } else {
        $rank += 1 + $skip;
        $skip = 0;
    }

    $classPositions[$item['student_id']] = $rank;

    $previousAverage = $item['average'];
}

   return view('results.broadsheet', compact(
    'class',
    'subjects',
    'results',
    'termId',
    'sessionId',
    'classPositions'
));
}

public function downloadBroadsheet(Request $request, $class_id)
{
    // Load the selected class and its students
    $class = SchoolClass::with('students')
        ->where('school_id', Auth::user()->school_id)
        ->findOrFail($class_id);

    $termId = $request->term_id;
    $sessionId = $request->session_id;
    $term = \App\Models\Term::find($termId);
$session = \App\Models\AcademicSession::find($sessionId);

   $subjects = $class->subjects()
    ->orderBy('name')
    ->distinct()
    ->get();

    $results = Result::whereIn('student_id', $class->students->pluck('id'))
        ->where('term_id', $termId)
        ->where('session_id', $sessionId)
        ->get()
        ->keyBy(function ($result) {
            return $result->student_id . '_' . $result->subject_id;
        });

    // Calculate positions
    $positions = [];

    foreach ($class->students as $student) {

        $studentResults = $results->filter(function ($result) use ($student) {
            return $result->student_id == $student->id;
        });

        $average = $studentResults->count()
            ? round($studentResults->avg('total_score'), 2)
            : 0;

        $positions[] = [
            'student_id' => $student->id,
            'average'    => $average,
        ];
    }

    usort($positions, function ($a, $b) {
        return $b['average'] <=> $a['average'];
    });

    $rank = 0;
    $skip = 0;
    $previousAverage = null;
    $classPositions = [];

    foreach ($positions as $item) {

        if ($previousAverage === $item['average']) {
            $skip++;
        } else {
            $rank += 1 + $skip;
            $skip = 0;
        }

        $classPositions[$item['student_id']] = $rank;

        $previousAverage = $item['average'];
    }

   $pdf = Pdf::loadView('results.broadsheet-pdf', compact(
    'class',
    'subjects',
    'results',
    'termId',
    'sessionId',
    'term',
    'session',
    'classPositions'
))->setPaper('a3', 'landscape');

    return $pdf->download(
        'Broadsheet_' . str_replace(' ', '_', $class->name) . '.pdf'
    );
}
}
