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
use Illuminate\Support\Facades\Storage;
   
   use App\Mail\ResultMail;
   use App\Services\ResultService;
   use Illuminate\Validation\Rule;

   use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\StudentFee;


   

  




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



   public function sendResultWhatsapp($studentId)
   {
       $student = Student::with(['school', 'schoolClass', 'guardian', 'results.subject'])->findOrFail($studentId);
   
       // Get term and session (defaults to latest if not provided)
       $term = request('term_id') ? Term::find(request('term_id')) : Term::latest()->first();
       $session = request('session_id') ? AcademicSession::find(request('session_id')) : AcademicSession::latest()->first();
   
       $results = Result::where('student_id', $studentId)
           ->where('term_id', $term->id)
           ->where('session_id', $session->id)
           ->with('subject')
           ->get();
   
       if ($results->isEmpty()) {
           return redirect()->back()->with('warning', '⚠️ No results found for this student.');
       }
   
       // Compute position in class
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
           $position = $ranked ? array_search($studentId, $ranked) + 1 : null;
           $total_students = count($ranked);
       }
   
       // Generate PDF
       $pdf = Pdf::loadView('results.pdf', [
           'student' => $student,
           'results' => $results,
           'term' => $term,
           'session' => $session,
           'school' => $student->school,
           'position' => $position,
           'total_students' => $total_students,
       ]);
   
       // Truehost public directory
       $folder = $_SERVER['DOCUMENT_ROOT'] . '/results';
   
       if (!file_exists($folder)) {
           mkdir($folder, 0777, true);
       }
   
       // File path
       $filePath = $folder . '/' . $student->id . '.pdf';
   
       // Save PDF
       $pdf->save($filePath);
   
       // Public URL
       $pdfUrl = url('results/' . $student->id . '.pdf');
   
       // Parent phone (international format)
       $parentPhone = preg_replace('/^0/', '234', $student->guardian_phone ?? $student->guardian->phone);
   
       // WhatsApp message
       $message = "Hello, your child's result is ready. Download PDF here: $pdfUrl";
       $encodedMessage = urlencode($message);
   
       // Redirect to WhatsApp
       return redirect("https://wa.me/{$parentPhone}?text={$encodedMessage}");
   }

 // download reciept

 

 public function downloadReceipt($studentId)
 {
     $student = Student::with(['school', 'schoolClass'])->findOrFail($studentId);
 
     // Get term & session (same pattern as results)
     $term = request('term') ?? Term::latest()->value('name');
     $session = request('session') ?? AcademicSession::latest()->value('name');
 
     // Fetch payments (THIS IS THE FIX)
     $payments = FeePayment::where('student_id', $studentId)
         ->where('term', $term)
         ->where('session', $session)
         ->orderBy('payment_date')
         ->get();
 
     if ($payments->isEmpty()) {
         return back()->with('warning', '⚠️ No payments found for this term.');
     }
 
     $totalPaid = $payments->sum('amount');
 
     // Generate PDF
$pdf = Pdf::loadView('receipts.pdf', [
    'student'   => $student,
    'payments'  => $payments,
    'term'      => $term,
    'session'   => $session,
    'school'    => $student->school,
    'totalPaid' => $totalPaid,
]);

// Sanitize for filename
$safeTerm = str_replace(['/', '\\', ' '], '-', $term);
$safeSession = str_replace(['/', '\\'], '-', $session);

// USE the sanitized values here
return $pdf->download(
    'receipt_' .
    $student->admission_number . '_' .
    $safeTerm . '_' .
    $safeSession . '.pdf'
);





 }
 
// send reciept to whatsapp





public function sendReceiptWhatsapp($studentId)
{
    $student = Student::with(['school', 'schoolClass', 'guardian'])->findOrFail($studentId);

    // Get all payments for this student
    $payments = FeePayment::where('student_id', $studentId)->get();

    if ($payments->isEmpty()) {
        return back()->with('warning', '⚠️ No fee payments found for this student.');
    }

    // ✅ Derive term & session from payments (SAFE)
    $term = $payments->first()->term ?? '—';
    $session = $payments->first()->session ?? '—';

    // Generate PDF
    $pdf = Pdf::loadView('receipts.pdf', [
        'student'  => $student,
        'payments' => $payments,
        'school'   => $student->school,
        'term'     => $term,
        'session'  => $session,
    ]);

    // Public directory (same pattern as result)
    $folder = $_SERVER['DOCUMENT_ROOT'] . '/receipts';

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $filePath = $folder . '/' . $student->id . '.pdf';
    $pdf->save($filePath);

    // Public URL
    $pdfUrl = url('receipts/' . $student->id . '.pdf');

    $parentPhone = $student->guardian->phone ?? '';

    // remove everything except digits
    $parentPhone = preg_replace('/\D+/', '', $parentPhone);
    
    // Nigerian format fixes
    if (str_starts_with($parentPhone, '0')) {
        $parentPhone = '234' . substr($parentPhone, 1);
    }
    
    if (str_starts_with($parentPhone, '2340')) {
        $parentPhone = '234' . substr($parentPhone, 4);
    }
    
    // WhatsApp message
    $message = "Hello, your child's school fee receipt is ready. Download PDF here: $pdfUrl";
    $encodedMessage = urlencode($message);

    return redirect("https://wa.me/{$parentPhone}?text={$encodedMessage}");
}





 


  


   
   

   

   


}
