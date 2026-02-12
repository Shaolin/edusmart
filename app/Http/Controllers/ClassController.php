<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * List classes
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin sees all classes in their school
            $classes = SchoolClass::with('formTeacher.user')
                        ->where('school_id', $user->school_id)
                        ->paginate(20);
        } else {
            // Teacher only sees their own class in their school
            $classes = SchoolClass::with('formTeacher.user')
                        ->where('school_id', $user->school_id)
                        ->where('form_teacher_id', $user->teacher->id ?? null)
                        ->paginate(20);
        }

        return view('classes.index', compact('classes'));
    }

    /**
     * Show a single class
     */
  
       
    
    
     public function show(Request $request, $id)
     {
         $user = Auth::user();
     
         // 1️⃣ UI term input (from dropdown) - normalize to lowercase
         $uiTerm = strtolower($request->query('term', 'first'));
     
         // 2️⃣ Map UI term -> fee_payments term
         $termMap = [
             'first'  => 'First Term',
             'second' => 'Second Term',
             'third'  => 'Third Term',
         ];
     
         $paymentTerm = $termMap[$uiTerm] ?? 'First Term'; // for fee_payments table
         $feeTerm     = $uiTerm;                           // for fees table (first, second, third)
     
         $activeSession = $request->query('session', '2025/2026');
         $feeFilter     = $request->query('fee_status', 'all');
     
         // 3️⃣ Load class
         $class = SchoolClass::with('formTeacher.user')->where('school_id', $user->school_id)->findOrFail($id);
     
         // 4️⃣ Active fee for this term/session (fees table)
         $activeFee = $class->fees()
             ->where('term', $feeTerm)
             ->where('session', $activeSession)
             ->latest()
             ->first();
     
         $latestFee = $activeFee?->amount ?? 0; // ✅ Total Fee per term
     
         // 5️⃣ Students with only payments for this term/session
         $studentsQuery = $class->students()
             ->with(['feePayments' => function ($q) use ($paymentTerm, $activeSession) {
                 $q->where('term', $paymentTerm)
                   ->where('session', $activeSession);
             }]);
     
         // 6️⃣ Fee filters
         if ($feeFilter !== 'all') {
             $studentsQuery->where(function ($q) use ($feeFilter, $latestFee, $paymentTerm, $activeSession) {
     
                 if ($feeFilter === 'fully-paid') {
                     $q->whereHas('feePayments', function ($p) use ($latestFee, $paymentTerm, $activeSession) {
                         $p->where('term', $paymentTerm)
                           ->where('session', $activeSession)
                           ->groupBy('student_id')
                           ->havingRaw('SUM(amount) >= ?', [$latestFee]);
                     });
                 }
     
                 if ($feeFilter === 'partial') {
                     $q->whereHas('feePayments', function ($p) use ($latestFee, $paymentTerm, $activeSession) {
                         $p->where('term', $paymentTerm)
                           ->where('session', $activeSession)
                           ->groupBy('student_id')
                           ->havingRaw('SUM(amount) > 0 AND SUM(amount) < ?', [$latestFee]);
                     });
                 }
     
                 if ($feeFilter === 'unpaid') {
                     $q->whereDoesntHave('feePayments', function ($p) use ($paymentTerm, $activeSession) {
                         $p->where('term', $paymentTerm)
                           ->where('session', $activeSession);
                     });
                 }
             });
         }
     
         $students = $studentsQuery->paginate(20)->withQueryString();
     
         return view('classes.show', compact(
             'class',
             'students',
             'latestFee',
             'feeFilter',
             'uiTerm',       // for dropdown selected
             'paymentTerm',  // for payment queries
             'activeSession'
         ));
     }
     
    
    
     
    
    
    

    /**
     * Create form (Admin only)
     */
    public function create()
    {
        $this->authorizeAdmin();

        $user = Auth::user();
        $teachers = Teacher::with('user')
                    ->where('school_id', $user->school_id)
                    ->get();
        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('classes.create', compact('teachers', 'classes'));
    }

    /**
     * Store class (Admin only)
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
                Rule::unique('classes')->where(function ($query) use ($user, $request) {
                    return $query->where('school_id', $user->school_id)
                                 ->where('section', $request->section);
                }),
            ],
        
            'section'         => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
            
            'next_class_id' => 'nullable|exists:classes,id',

        ]);
        
        // Assign school_id automatically
        $validated['school_id'] = $user->school_id;

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    /**
     * Edit form (Admin only)
     */
    public function edit(SchoolClass $class)
    {
        $this->authorizeAdmin();

        $user = Auth::user();
        $teachers = Teacher::with('user')->where('school_id', $user->school_id)->get();
        $classes = SchoolClass::where('school_id', $user->school_id)
                    ->where('id', '!=', $class->id)
                    ->get();

        return view('classes.edit', compact('class', 'teachers', 'classes'));
    }

    /**
     * Update (Admin only)
     */
    public function update(Request $request, SchoolClass $class)
    {
        $this->authorizeAdmin();
    
        $user = Auth::user();
    
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')
                    ->where(function ($query) use ($user, $request) {
                        return $query->where('school_id', $user->school_id)
                                     ->where('section', $request->section);
                    })
                    ->ignore($class->id),
            ],
    
            'section'         => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
    
            'next_class_id' => [
                'nullable',
                'exists:classes,id',
                'not_in:' . $class->id
            ],
        ]);
    
        $class->update($validated);
    
        return redirect()
            ->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }
    

    /**
     * Delete (Admin only)
     */
    public function destroy(SchoolClass $class)
    {
        $this->authorizeAdmin();

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }

    /**
     * View students in a class
     */
    public function students(SchoolClass $class)
    {
        $user = Auth::user();

        if ($user->role === 'teacher' && $class->form_teacher_id !== $user->teacher->id) {
            abort(403, 'Unauthorized');
        }

        $students = $class->students()->with('guardian')
                    ->where('school_id', $user->school_id)
                    ->paginate(20);

        return view('classes.students', compact('class', 'students'));
    }

    /**
     * Promote students
     */
    public function promoteClass($classId)
    {
        $user = Auth::user();
        $class = SchoolClass::with('nextClass')
                    ->where('school_id', $user->school_id)
                    ->findOrFail($classId);

        if (!$class->nextClass) {
            return redirect()->route('classes.index')
                ->with('error', "Class {$class->name} has no next class assigned.");
        }

        if ($class->students()->count() === 0) {
            return redirect()->route('classes.index')
                ->with('info', "No students found in {$class->name} to promote.");
        }

        $class->students()->update([
            'class_id' => $class->next_class_id,
        ]);

        return redirect()->route('classes.index')
            ->with('success', "All students promoted from {$class->name} to {$class->nextClass->name}.");
    }

    /**
     * Helper: Only allow admins
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }
}
