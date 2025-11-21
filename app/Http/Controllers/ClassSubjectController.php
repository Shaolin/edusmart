<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClassSubjectController extends Controller
{
    /**
     * Show form to assign a subject to a class
     */
    public function create()
    {
        $user = Auth::user();

        $classes  = SchoolClass::where('school_id', $user->school_id)->get();
        $subjects = Subject::where('school_id', $user->school_id)->get();
        $teachers = Teacher::where('school_id', $user->school_id)->get();

        return view('class-subjects.create', compact('classes', 'subjects', 'teachers'));
    }

    /**
     * Store a new class-subject-teacher assignment
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Ensure all IDs belong to this school
        $class   = SchoolClass::where('id', $request->class_id)
                              ->where('school_id', $user->school_id)
                              ->firstOrFail();

        $teacher = Teacher::where('id', $request->teacher_id)
                          ->where('school_id', $user->school_id)
                          ->firstOrFail();

        $subject = Subject::where('id', $request->subject_id)
                          ->where('school_id', $user->school_id)
                          ->firstOrFail();

        DB::table('class_subject_teacher')->updateOrInsert(
            [
                'class_id'   => $class->id,
                'subject_id' => $subject->id,
            ],
            [
                'teacher_id' => $teacher->id,
                'school_id'  => $user->school_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Subject assigned to class successfully!');
    }

    /**
     * List assignments
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DB::table('class_subject_teacher')
            ->join('teachers', 'class_subject_teacher.teacher_id', '=', 'teachers.id')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('classes', 'class_subject_teacher.class_id', '=', 'classes.id')
            ->where('classes.school_id', $user->school_id)
            ->select(
                'class_subject_teacher.id',
                'users.name as teacher_name',
                'subjects.name as subject_name',
                'subjects.level as subject_level',
                'classes.name as class_name'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                  ->orWhere('subjects.name', 'like', "%$search%")
                  ->orWhere('classes.name', 'like', "%$search%");
            });
        }

        if ($request->filled('teacher_id')) {
            $query->where('teachers.id', $request->teacher_id);
        }

        if ($request->filled('class_id')) {
            $query->where('classes.id', $request->class_id);
        }

        $assignments = $query->paginate(10)->withQueryString();

        $teachers = Teacher::where('teachers.school_id', $user->school_id)  // <-- add table name
        ->join('users', 'teachers.user_id', '=', 'users.id')
        ->select('teachers.id', 'users.name')
        ->orderBy('users.name')
        ->get();
    

        $classes = SchoolClass::where('school_id', $user->school_id)
                              ->orderBy('name')
                              ->get();

        return view('class-subjects.index', compact('assignments', 'teachers', 'classes'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = Auth::user();

        $assignment = DB::table('class_subject_teacher')->where('id', $id)->first();
        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        $classes  = SchoolClass::where('school_id', $user->school_id)->get();
        $subjects = Subject::where('school_id', $user->school_id)->get();
        $teachers = Teacher::where('school_id', $user->school_id)->get();

        return view('class-subjects.edit', compact('assignment', 'classes', 'subjects', 'teachers'));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Ensure all IDs belong to this school
        $class   = SchoolClass::where('id', $request->class_id)
                              ->where('school_id', $user->school_id)
                              ->firstOrFail();

        $teacher = Teacher::where('id', $request->teacher_id)
                          ->where('school_id', $user->school_id)
                          ->firstOrFail();

        $subject = Subject::where('id', $request->subject_id)
                          ->where('school_id', $user->school_id)
                          ->firstOrFail();

        DB::table('class_subject_teacher')
            ->where('id', $id)
            ->update([
                'class_id'   => $class->id,
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'updated_at' => now(),
            ]);

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete assignment
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Optionally, ensure the assignment belongs to the school
        $assignment = DB::table('class_subject_teacher')->where('id', $id)->first();
        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        $class = SchoolClass::where('id', $assignment->class_id)
                            ->where('school_id', $user->school_id)
                            ->firstOrFail();

        DB::table('class_subject_teacher')->where('id', $id)->delete();

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Assignment deleted successfully!');
    }
}
