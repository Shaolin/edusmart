<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin'); // Only admins can access
    }

    /**
     * Display a listing of teachers for the admin's school.
     */
    public function index()
    {
        $user = Auth::user();
        $teachers = Teacher::with('user')
            ->where('school_id', $user->school_id)
            ->paginate(20);

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
     * Store a newly created teacher with school_id.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'staff_id'     => [
                'nullable',
                'string',
                Rule::unique('teachers')->where(fn($query) => $query->where('school_id', $user->school_id)),
            ],
            'qualification'=> 'nullable|string|max:255',
            'specialization'=> 'nullable|string|max:255',
        ]);

        // Create user
        $userTeacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'school_id' => $user->school_id,
        ]);

      
        

        // Create teacher record
        Teacher::create([
            'user_id' => $userTeacher->id,
            'school_id' => $user->school_id,
            'staff_id' => $validated['staff_id'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
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
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password'     => 'nullable|string|min:6|confirmed',
            'staff_id'     => [
                'nullable',
                'string',
                Rule::unique('teachers')->ignore($teacher->id)->where(fn($query) => $query->where('school_id', $teacher->school_id)),
            ],
            'qualification'=> 'nullable|string|max:255',
            'specialization'=> 'nullable|string|max:255',
        ]);

        // Update linked user
        $teacher->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => !empty($validated['password'])
                ? Hash::make($validated['password'])
                : $teacher->user->password,
        ]);

        // Update teacher
        $teacher->update([
            'staff_id' => $validated['staff_id'] ?? $teacher->staff_id,
            'qualification' => $validated['qualification'] ?? $teacher->qualification,
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
