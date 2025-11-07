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
    public function show(Request $request, $id)
{
     $class = SchoolClass::with(['formTeacher.user', 'fees'])->findOrFail($id);
    // $class = SchoolClass::with([
    //     'students.feePayments',  // load fee payments
    //     'fees',                  // load fees for this class
    //     'formTeacher.user'
    // ])->findOrFail($id);

    // Filter by fee status
    $feeFilter = $request->query('fee_status', 'all'); // all by default

    // Latest fee for this class
    $latestFee = $class->fees->max('amount') ?? 0;

    // Base query for students
    $studentsQuery = $class->students()->with('feePayments');

    // Apply fee filter
    $studentsQuery->where(function($query) use ($feeFilter, $latestFee) {
        if ($feeFilter === 'fully-paid') {
            $query->whereHas('feePayments', function($q) use ($latestFee) {
                $q->selectRaw('student_id, SUM(amount) as total_paid')
                  ->groupBy('student_id')
                  ->havingRaw('SUM(amount) >= ?', [$latestFee]);
            });
        } elseif ($feeFilter === 'partial') {
            $query->whereHas('feePayments', function($q) use ($latestFee) {
                $q->selectRaw('student_id, SUM(amount) as total_paid')
                  ->groupBy('student_id')
                  ->havingRaw('SUM(amount) < ? AND SUM(amount) > 0', [$latestFee]);
            });
        } elseif ($feeFilter === 'unpaid') {
            $query->whereDoesntHave('feePayments');
        }
    });

    // Paginate results (20 per page)
    $students = $studentsQuery->paginate(20)->withQueryString();

    return view('classes.show', compact('class', 'students', 'latestFee', 'feeFilter'));
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
