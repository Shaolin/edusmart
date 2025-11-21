<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'staff_id' => 'nullable|string|unique:teachers,staff_id',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        $userTeacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'school_id' => $user->school_id, // assign school_id to user
        ]);

        Teacher::create([
            'user_id' => $userTeacher->id,
            'staff_id' => $validated['staff_id'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'school_id' => $user->school_id, // assign school_id to teacher
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
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:6|confirmed',
            'staff_id' => 'nullable|string|unique:teachers,staff_id,' . $teacher->id,
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        $teacher->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => !empty($validated['password'])
                ? Hash::make($validated['password'])
                : $teacher->user->password,
        ]);

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

// SubjectController

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
     * List subjects with optional search
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

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
    
        // $validated = $request->validate([
        //     'name' => [
        //         'required',
        //         'string',
        //         'max:255',
        //         Rule::unique('subjects')->where(function ($query) use ($request) {
        //             return $query
        //                 ->where('school_id', Auth::user()->school_id)
        //                 ->where('level', $request->level);
        //         }),
        //     ],
    
        //     'level' => 'required|in:Nursery,Primary,JSS,SSS',
        // ]);
        $validated = $request->validate([
            'name'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->where(function ($query) use ($request) {
                    return $query->where('level', $request->level)
                                 ->where('school_id', Auth::user()->school_id);
                }),
            ],
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);
        
    
        $validated['school_id'] = Auth::user()->school_id;
    
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
            'name'  => 'required|string|max:255|unique:subjects,name,' . $subject->id . ',id,school_id,' . Auth::user()->school_id,
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);

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


// ClassSubjectController

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
