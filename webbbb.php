<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Only allow admins
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }

    /**
     * List subjects for the logged-in school with optional search
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $subjects = Subject::where('school_id', $user->school_id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('level', 'like', "%{$search}%");
            })
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('subjects.index', compact('subjects', 'search'));
    }

    /**
     * Show form to create a new subject
     */
    public function create()
    {
        $this->authorizeAdmin();
        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];
        return view('subjects.create', compact('levels'));
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->where(function ($query) use ($request, $user) {
                    return $query->where('level', $request->level)
                                 ->where('school_id', $user->school_id);
                }),
            ],
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);
        
        $validated['name'] = trim($validated['name']);   // 🔥 FIX
        $validated['school_id'] = $user->school_id;

        

        
        Subject::create($validated);
        

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Show form to edit a subject
     */
    public function edit(Subject $subject)
    {
        $this->authorizeAdmin();
        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];
        return view('subjects.edit', compact('subject', 'levels'));
    }

    /**
     * Update a subject
     */
    public function update(Request $request, Subject $subject)
    {
        $this->authorizeAdmin();
        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($subject->id)->where(function ($query) use ($request) {
                    return $query->where('level', $request->level)
                                 ->where('school_id', auth()->user()->school_id);
                }),
            ],
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);
        
        $validated['name'] = trim($validated['name']);   // 🔥 FIX
        $validated['school_id'] = auth()->user()->school_id;
        
        $subject->update($validated);
        

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Delete a subject
     */
    public function destroy(Subject $subject)
    {
        $this->authorizeAdmin();
        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}


// new student controller

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

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


   

  




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




// whatsapp student list
public function bulk()
{
    $classes = SchoolClass::orderBy('name')->get();

    return view('students.bulk', compact('classes'));
}





public function bulkCreate()
{
    $classes = SchoolClass::where('school_id', Auth::user()->school_id)
        ->orderBy('name')
        ->get();

    return view('students.bulk-create', compact('classes'));
}

private function getNextAdmissionNumber($schoolId)
{
    $lastAdmission = Student::where('school_id', $schoolId)
        ->max('admission_number');

    return $lastAdmission ? ((int)$lastAdmission + 1) : 1;
}



  
public function bulkStore(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'bulk_data' => 'required|string',
        'class_id'  => 'required|exists:classes,id'
    ]);

    $lines = explode("\n", $request->bulk_data);

    $studentsToInsert = [];
    $errors = [];

    DB::beginTransaction();

    try {

        // ✅ Get starting admission number ONCE
        $nextAdmissionNumber = $this->getNextAdmissionNumber($user->school_id);

        foreach ($lines as $index => $line) {

            $lineNumber = $index + 1;
            $line = trim($line);

            if (empty($line)) continue;

            // ✅ Remove numbering
            $line = preg_replace('/^\d+[\.\-\)]\s*/', '', $line);

            // ✅ Detect delimiter
            if (strpos($line, '|') !== false) {
                $parts = explode('|', $line);
            } else {
                $parts = explode(',', $line);
            }

            $parts = array_map('trim', $parts);

            if (count($parts) !== 3) {
                $errors[] = "Line {$lineNumber}: Invalid format";
                continue;
            }

            [$studentName, $guardianName, $phone] = $parts;

            if (!$studentName || !$guardianName || !$phone) {
                $errors[] = "Line {$lineNumber}: Missing data";
                continue;
            }

            // ✅ Normalize phone
            $phone = preg_replace('/\s+/', '', $phone);

            // ✅ Guardian
            $guardian = Guardian::firstOrCreate(
                [
                    'phone' => $phone,
                    'school_id' => $user->school_id
                ],
                [
                    'name' => $guardianName
                ]
            );

            // ✅ Prevent duplicates
            $studentExists = Student::where('name', $studentName)
                ->where('guardian_id', $guardian->id)
                ->where('class_id', $request->class_id)
                ->exists();

            if ($studentExists) {
                $errors[] = "Line {$lineNumber}: Student already exists";
                continue;
            }

            $studentsToInsert[] = [
                'name' => $studentName,
                'guardian_id' => $guardian->id,
                'class_id' => $request->class_id,
                'school_id' => $user->school_id,

                // ✅ SAFE admission number
                'admission_number' => str_pad($nextAdmissionNumber++, 3, '0', STR_PAD_LEFT),

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($studentsToInsert)) {
            Student::insert($studentsToInsert);
        }

        DB::commit();

    } catch (\Exception $e) {

        DB::rollBack();
        throw $e;
    }

    return redirect()
        ->back()
        ->with('success', 'Bulk import completed')
        
        ->with('importErrors', $errors);

}

   
   

   

   


}
