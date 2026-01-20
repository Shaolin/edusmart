<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
    
        // Load class with fees
        $class = SchoolClass::with(['formTeacher.user', 'fees'])
                    ->where('school_id', $user->school_id)
                    ->findOrFail($id);
    
        // Get selected term from query (default to 'first')
        $selectedTerm = $request->query('term', 'first');
    
        // You can get active session dynamically from your sessions table
        $activeSession = $request->query('session', '2025/2026');
    
        // Filter fees for selected term & session
        $feesForTerm = $class->fees->where('term', $selectedTerm)
                                   ->where('session', $activeSession);
    
        $latestFee = $feesForTerm->max('amount') ?? 0;
    
        // Students query
        $studentsQuery = $class->students()->with(['feePayments']);
    
        // Apply fee status filter (fully-paid, partial, unpaid)
        $feeFilter = $request->query('fee_status', 'all');
    
        $studentsQuery->where(function($query) use ($feeFilter, $latestFee, $selectedTerm, $activeSession) {
            if ($feeFilter === 'fully-paid') {
                $query->whereHas('feePayments', function($q) use ($latestFee, $selectedTerm, $activeSession) {
                    $q->where('term', $selectedTerm)
                      ->where('session', $activeSession)
                      ->selectRaw('student_id, SUM(amount) as total_paid')
                      ->groupBy('student_id')
                      ->havingRaw('SUM(amount) >= ?', [$latestFee]);
                });
            } elseif ($feeFilter === 'partial') {
                $query->whereHas('feePayments', function($q) use ($latestFee, $selectedTerm, $activeSession) {
                    $q->where('term', $selectedTerm)
                      ->where('session', $activeSession)
                      ->selectRaw('student_id, SUM(amount) as total_paid')
                      ->groupBy('student_id')
                      ->havingRaw('SUM(amount) < ? AND SUM(amount) > 0', [$latestFee]);
                });
            } elseif ($feeFilter === 'unpaid') {
                $query->whereDoesntHave('feePayments', function($q) use ($selectedTerm, $activeSession) {
                    $q->where('term', $selectedTerm)
                      ->where('session', $activeSession);
                });
            }
        });
    
        $students = $studentsQuery->paginate(20)->withQueryString();
    
        return view('classes.show', compact('class', 'students', 'latestFee', 'feeFilter', 'selectedTerm', 'activeSession'));
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
                Rule::unique('classes')->where(fn($query) => $query->where('school_id', $user->school_id)),
            ],
            'section' => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
            'next_class_id' => 'nullable|exists:school_classes,id',
        ]);

        $validated['school_id'] = $user->school_id;

        SchoolClass::create($validated);

        return redirect()->route('classes.index')
                         ->with('success', 'Class created successfully.');
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
