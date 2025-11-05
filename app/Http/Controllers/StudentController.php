<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    

     public function index()
{
    $user = Auth::user();

    // Base query
    
    $query = Student::with(['schoolClass', 'guardian']);


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
        $formClass = $user->teacher->formClasses()->first(); // assuming one form class
        if ($formClass) {
            $query->where('class_id', $formClass->id);
        } else {
            // teacher with no assigned class -> return empty collection (but still pass $classes)
            $students = collect();
            $classes = SchoolClass::all();
            return view('students.index', compact('students', 'classes'));
        }
    }

    // Paginate and preserve query string for links
    $students = $query->paginate(20)->appends(request()->query());
     // ALWAYS pass $classes so the select box in the view won't cause undefined variable
     $classes = SchoolClass::all();

     return view('students.index', compact('students', 'classes'));
 }

   

     


    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        $guardians = Guardian::all();

        return view('students.create', compact('classes', 'guardians'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'gender'           => 'required|in:male,female',
            'admission_number'     => 'required|unique:students,admission_number',
            'class_id'         => 'required|exists:classes,id',

            // Either select an existing guardian or provide new one
            'guardian_id'      => 'nullable|exists:guardians,id',
            'guardian_name'    => 'nullable|string|max:255',
            'guardian_phone'   => 'nullable|string|max:20',
        ]);

        // Case 1: If guardian_id is provided, just use it
        if (!empty($validated['guardian_id'])) {
            $guardianId = $validated['guardian_id'];
        }
        // Case 2: If no guardian_id, create a new guardian
        else {
            $request->validate([
                'guardian_name'  => 'required|string|max:255',
                'guardian_phone' => 'required|string|max:20|unique:guardians,phone',
            ]);

            $guardian = Guardian::create([
                'name'  => $request->guardian_name,
                'phone' => $request->guardian_phone,
            ]);

            $guardianId = $guardian->id;
        }

        // Finally create student
        Student::create([
            'name'         => $validated['name'],
            'gender'       => $validated['gender'],
            'admission_number' => $validated['admission_number'],
            'class_id'     => $validated['class_id'],
            'guardian_id'  => $guardianId,
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
        $classes = SchoolClass::all();
        $guardians = Guardian::all();

        return view('students.edit', compact('student', 'classes', 'guardians'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'gender'           => 'required|in:male,female',
            'admission_number'     => 'required|unique:students,admission_number,' . $student->id,
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
                'guardian_phone' => 'required|string|max:20|unique:guardians,phone',
            ]);

            $guardian = Guardian::create([
                'name'  => $request->guardian_name,
                'phone' => $request->guardian_phone,
            ]);

            $guardianId = $guardian->id;
        }

        $student->update([
            'name'         => $validated['name'],
            'gender'       => $validated['gender'],
            'admission_number' => $validated['admission_number'],
            'class_id'     => $validated['class_id'],
            'guardian_id'  => $guardianId,
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
}
