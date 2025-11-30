<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Term;
use App\Models\Result;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;


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

}







