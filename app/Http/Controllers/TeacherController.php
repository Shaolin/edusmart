<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function __construct()
    {
        // Apply the 'admin' middleware to all routes in this controller
        $this->middleware('admin');
    }

    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        $teachers = Teacher::with('user')->paginate(20);
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher (with linked user).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // User fields
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',

            // Teacher fields
            'staff_id'       => 'nullable|string|unique:teachers,staff_id',
            'qualification'  => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        // Create the user with role = teacher
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'teacher',
        ]);

        // Create the teacher profile
        Teacher::create([
            'user_id'        => $user->id,
            'staff_id'       => $validated['staff_id'] ?? null,
            'qualification'  => $validated['qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
        ]);

        return redirect()->route('teachers.index')
                         ->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified teacher profile.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher and linked user.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            // User fields
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:6|confirmed',

            // Teacher fields
            'staff_id'       => 'nullable|string|unique:teachers,staff_id,' . $teacher->id,
            'qualification'  => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        // Update user
        $teacher->user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'password' => !empty($validated['password'])
                ? Hash::make($validated['password'])
                : $teacher->user->password,
        ]);

        // Update teacher profile
        $teacher->update([
            'staff_id'       => $validated['staff_id'] ?? $teacher->staff_id,
            'qualification'  => $validated['qualification'] ?? $teacher->qualification,
            'specialization' => $validated['specialization'] ?? $teacher->specialization,
        ]);

        return redirect()->route('teachers.index')
                         ->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified teacher (and linked user).
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->user()->delete(); // cascades to teacher via FK
        return redirect()->route('teachers.index')
                         ->with('success', 'Teacher deleted successfully.');
    }
}
