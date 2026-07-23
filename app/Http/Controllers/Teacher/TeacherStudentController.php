<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Result;
use App\Models\School;
use App\Models\Student;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;

        // Flatten all students across teacher's classes
        $students = $teacher->formClasses->flatMap->students;

        // --- Filtering ---
        if ($request->filled('name')) {
            $students = $students->filter(fn($s) => str_contains(strtolower($s->name), strtolower($request->name)));
        }

        if ($request->filled('class_id')) {
            $students = $students->filter(fn($s) => $s->class_id == $request->class_id);
        }

        if ($request->filled('gender')) {
            $students = $students->filter(fn($s) => strtolower($s->gender) == strtolower($request->gender));
        }

        // --- Pagination ---
        $perPage = 10; // change this if you want more/less per page
        $currentPage = $request->input('page', 1);
        $currentItems = $students->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedStudents = new LengthAwarePaginator(
            $currentItems,
            $students->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $classes = $teacher->formClasses;
        $totalStudents = $students->count();

// Correct view path
return view('teachers.students.index', [
    'students' => $paginatedStudents,
    'classes' => $classes,
    'totalStudents' => $totalStudents,
]);
    }


    public function sendResultWhatsapp($studentId, Request $request)
{
 

    $teacher = auth()->user()->teacher;

   

    // Make sure teacher manages this class
    $formClassIds = $teacher->formClasses()->pluck('id');
    $student = Student::with(['school', 'schoolClass'])->findOrFail($studentId);

    if (! $formClassIds->contains($student->class_id)) {
        abort(403, 'You are not authorized to send results for this student.');
    }

    // Get term & session (fallback to latest)
    $term = $request->term_id ? Term::find($request->term_id) : Term::latest()->first();
    $session = $request->session_id ? AcademicSession::find($request->session_id) : AcademicSession::latest()->first();

    // Fetch results
    $results = Result::with('subject')
        ->where('student_id', $student->id)
        ->where('term_id', $term->id)
        ->where('session_id', $session->id)
        ->get();

    if ($results->isEmpty()) {
        return back()->with('warning', '⚠️ No results found for this student.');
    }

    // Compute position
    $classStudentIds = Student::where('class_id', $student->class_id)->pluck('id');

    $classTotals = Result::select('student_id', DB::raw('SUM(total_score) as total'))
        ->whereIn('student_id', $classStudentIds)
        ->where('term_id', $term->id)
        ->where('session_id', $session->id)
        ->groupBy('student_id')
        ->orderByDesc('total')
        ->get();

    $total_students = $classTotals->count();
    $position = null;

    if ($total_students > 0) {
        $pos = $classTotals->search(fn($row) => $row->student_id == $student->id);
        if ($pos !== false) {
            $position = $pos + 1;
        }
    }

    // School info
    $school = School::find(auth()->user()->school_id);

    // PDF data
    $data = [
        'student' => $student,
        'results' => $results,
        'term' => $term,
        'session' => $session,
        'school' => $school,
        'position' => $position,
        'total_students' => $total_students,
    ];

    // Generate PDF using the teacher's view
    $pdf = Pdf::loadView('teachers.results.pdf', $data);

    // Truehost public folder (/public_html/results)
    $folder = $_SERVER['DOCUMENT_ROOT'] . '/results';

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // Save file
    $filePath = $folder . '/' . $student->id . '.pdf';
    $pdf->save($filePath);

    // Public link to PDF
    $pdfUrl = url('results/' . $student->id . '.pdf');

    // Parent phone format
    $phone = $student->guardian_phone ?? $student->guardian->phone;
    $parentPhone = preg_replace('/^0/', '234', $phone);

    // WhatsApp message
    $message = "Hello, your child's result is ready. Download PDF here: $pdfUrl";
    $encodedMessage = urlencode($message);

    // Redirect
    return redirect("https://wa.me/{$parentPhone}?text={$encodedMessage}");


    
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

public function sendAnnualResultWhatsapp($student_id, $session_id)
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

    // Determine promotion status
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
$mathPassed = $mathematics && $mathematics->average >= 40;

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

    

$pdf = Pdf::loadView('teachers.results.annual_pdf', [
    'student'           => $student,
    'session'           => $session,
    'school'            => $school,
    'setting'           => $setting,
    'cumulativeResults' => $cumulativeResults,
    'annualTotal'       => $annualTotal,
    'annualAverage'     => $annualAverage,
    'annualPosition'    => $annualPosition,
    'totalStudents'     => $totalStudents,
    'promotionStatus'   => $promotionStatus, 
]);

$pdf->setPaper('A4', 'portrait');

// Save to public folder
$folder = $_SERVER['DOCUMENT_ROOT'] . '/results';

if (! file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$fileName = 'annual_' . $student->id . '.pdf';
$filePath = $folder . '/' . $fileName;

$pdf->save($filePath);

// Public URL
$pdfUrl = url('results/' . $fileName);

// Parent phone
$phone = $student->guardian_phone ?? $student->guardian->phone;

// Normalize phone number
$parentPhone = preg_replace('/\D/', '', $phone);

if (str_starts_with($parentPhone, '0')) {
    $parentPhone = '234' . substr($parentPhone, 1);
}

// WhatsApp message
$message = "Hello, your child's Annual Result is ready. Download it here: {$pdfUrl}";

return redirect(
    'https://wa.me/' . $parentPhone . '?text=' . urlencode($message)
);
}

}







