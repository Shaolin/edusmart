<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{
    public function create()
    {
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        return view('class-subjects.create', compact('classes', 'subjects', 'teachers'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        DB::table('class_subject_teacher')->updateOrInsert(
            [
                'class_id'   => $request->class_id,
                'subject_id' => $request->subject_id,
            ],
            [
                'teacher_id' => $request->teacher_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Subject assigned to class successfully!');
    }


    /** ----------------------------------------------------------
     *                     SHOW LIST PAGE
     * ---------------------------------------------------------- */
    public function index(Request $request)
    {
        $query = DB::table('class_subject_teacher')
            ->join('teachers', 'class_subject_teacher.teacher_id', '=', 'teachers.id')
            ->join('users', 'teachers.user_id', '=', 'users.id')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('classes', 'class_subject_teacher.class_id', '=', 'classes.id')
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

        $teachers = DB::table('teachers')
            ->join('users', 'teachers.user_id', 'users.id')
            ->select('teachers.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        $classes = DB::table('classes')->orderBy('name')->get();

        return view('class-subjects.index', compact('assignments', 'teachers', 'classes'));
    }


    /** ----------------------------------------------------------
     *                     EDIT FORM
     * ---------------------------------------------------------- */
    public function edit($id)
    {
        $assignment = DB::table('class_subject_teacher')->where('id', $id)->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        return view('class-subjects.edit', compact('assignment', 'classes', 'subjects', 'teachers'));
    }


    /** ----------------------------------------------------------
     *                     UPDATE ASSIGNMENT
     * ---------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        DB::table('class_subject_teacher')
            ->where('id', $id)
            ->update([
                'class_id'   => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'subject_id' => $request->subject_id,
                'updated_at' => now(),
            ]);

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Assignment updated successfully!');
    }


    /** ----------------------------------------------------------
     *                     DELETE ASSIGNMENT
     * ---------------------------------------------------------- */
    public function destroy($id)
    {
        DB::table('class_subject_teacher')->where('id', $id)->delete();

        return redirect()->route('class_subject_teacher.index')
            ->with('success', 'Assignment deleted successfully!');
    }
}
