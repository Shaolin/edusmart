<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    /**
     * List classes
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin sees all classes
            $classes = SchoolClass::with('formTeacher.user')->paginate(20);
        } else {
            // Teacher only sees their own class
            $classes = SchoolClass::with('formTeacher.user')
                        ->where('form_teacher_id', $user->teacher->id ?? null)
                        ->paginate(20);
        }

        return view('classes.index', compact('classes'));
    }

    /**
     * Show a single class
     */
    // public function show(SchoolClass $class)
    // {
    //     $user = Auth::user();

    //     if ($user->role === 'teacher' && $class->form_teacher_id !== $user->teacher->id) {
    //         abort(403, 'Unauthorized');
    //     }

        
    //     $class->load(['formTeacher.user', 'students.guardian']);

    //     return view('classes.show', compact('class'));
    // }
    public function show($id)
    {
        $class = SchoolClass::with('fees')->findOrFail($id);
    
        // Get students of this class with fee payment summary
        $students = Student::where('class_id', $id)
            ->with(['feePayments' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            }])
            ->paginate(20);
    
        // Pre-calculate the total fee amount for this class
        $classTotalFee = $class->fees->sum('amount');
    
        // Map additional computed fields (without breaking pagination)
        $students->getCollection()->transform(function ($student) use ($classTotalFee) {
            $totalPaid = $student->feePayments->sum('amount');
            $balance = max($classTotalFee - $totalPaid, 0);
            $lastPayment = $student->feePayments->first();
    
            $student->total_fee = $classTotalFee;
            $student->total_paid = $totalPaid;
            $student->balance = $balance;
            $student->last_payment_date = $lastPayment ? $lastPayment->payment_date : null;
    
            return $student;
        });
    
        return view('classes.show', compact('class', 'students', 'classTotalFee'));
    }
    


    /**
     * Create form (Admin only)
     */
    public function create()
    {
        $this->authorizeAdmin();

        $teachers = Teacher::with('user')->get();
        $classes = SchoolClass::all(); // fetch all classes for the "Next Class" dropdown
        
        return view('classes.create', compact('teachers', 'classes'));
    }


    /**
     * Store class (Admin only)
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:classes,name',
            'section'         => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
            'next_class_id' => 'nullable|exists:school_classes,id',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    /**
     * Edit form (Admin only)
     */
    public function edit(SchoolClass $class)
    {
        $this->authorizeAdmin();

        $teachers = Teacher::with('user')->get();
        $classes  = SchoolClass::where('id', '!=', $class->id)->get(); // exclude itself
        return view('classes.edit', compact('class', 'teachers', 'classes'));
    }

    /**
     * Update (Admin only)
     */
    public function update(Request $request, SchoolClass $class)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:classes,name,' . $class->id,
            'section'         => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
            'next_class_id'   => ['nullable','exists:classes,id','not_in:'.$class->id],
      
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
        
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

        $students = $class->students()->with('guardian')->paginate(20);

        return view('classes.students', compact('class', 'students'));
    }
  // promote students
    public function promoteClass($classId)
    {
        $class = SchoolClass::with('nextClass')->findOrFail($classId);
    
        // Safety check
        if (!$class->nextClass) {
            return redirect()->route('classes.index')
                ->with('error', "Class {$class->name} has no next class assigned.");
        }
    
        // If there are no students in this class
        if ($class->students()->count() === 0) {
            return redirect()->route('classes.index')
                ->with('info', "No students found in {$class->name} to promote.");
        }
    
        // Promote all students
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
