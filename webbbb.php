<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ===============================
        // Teacher Dashboard
        // ===============================
        if ($user->role === 'teacher' && $user->teacher) {
            // Get the class where this teacher is the form teacher
            $class = $user->teacher->formClasses()->first();

            if ($class) {
                // Students in this class
                $students = $class->students()->with('guardian')->paginate(20);

                // Total students count
                $totalStudents = $students->total();

                // Unique guardians
                $guardians = $students->pluck('guardian')->filter();

                return view('dashboard.teacher', [
                    'class'         => $class,
                    'students'      => $students,
                    'totalStudents' => $totalStudents,
                    'guardians'     => $guardians,
                ]);
            }

            // Teacher with no assigned class
            return view('dashboard.teacher', [
                'class'         => null,
                'students'      => collect(),
                'totalStudents' => 0,
                'guardians'     => collect(),
            ]);
        }

        // ===============================
        // Admin Dashboard
        // ===============================
        return view('dashboard.index', [
            'totalStudents'  => Student::count(),
            'totalTeachers'  => Teacher::count(),
            'totalClasses'   => SchoolClass::count(),
            'totalGuardians' => Guardian::count(),
            'totalFees'      => Fee::sum('amount'), // 💰 Total defined fee amounts
        ]);
    }
}

// dashboard index.blade

<x-app-layout>
 
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">
    
            <!-- Dashboard Title -->
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                Dashboard Overview
            </h2>
    
            <!-- Right-side: Welcome + Toggle -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
    
                <!-- Welcome Message -->
                <h2 class="font-semibold text-lg sm:text-xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                    {{-- Welcome back, <span class="font-semibold">{{ auth()->user()->name }}</span>! --}}
                    Welcome back, {{ auth()->user()->name }}
                </h2>
    
             
            </div>
    
        </div>
    </x-slot>
    
    

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <!-- Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- Card Template Example -->
            @php
                $cards = [
                    ['name' => 'Students', 'route' => 'students.index', 'count' => $totalStudents, 'color' => 'blue'],
                    ['name' => 'Teachers', 'route' => 'teachers.index', 'count' => $totalTeachers, 'color' => 'green'],
                    ['name' => 'Classes', 'route' => 'classes.index', 'count' => $totalClasses, 'color' => 'purple'],
                    ['name' => 'Guardians', 'route' => 'guardians.index', 'count' => $totalGuardians, 'color' => 'yellow'],
                    ['name' => 'Fees', 'route' => 'fees.index', 'count' => 'View', 'color' => 'indigo'],
                    ['name' => 'Payments', 'route' => 'fee_payments.index', 'count' => 'View', 'color' => 'blue'],
                    ['name' => 'School', 'route' => 'schools.index', 'count' => 'Manage', 'color' => 'pink'],
                    ['name' => 'Results', 'route' => 'results.selectClass', 'count' => 'Manage', 'color' => 'red'],
                    ['name' => 'Subjects', 'route' => 'subjects.index', 'count' => 'Manage', 'color' => 'indigo'],
                    ['name' => 'Sessions', 'route' => 'sessions.index', 'count' => 'Manage', 'color' => 'indigo'],
                    ['name' => 'Terms', 'route' => 'terms.index', 'count' => 'Manage', 'color' => 'orange'],
                ];
            @endphp

          

            @foreach($cards as $card)
    <a href="{{ route($card['route']) }}"
       class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full
              dark:border dark:border-gray-700 dark:hover:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $card['name'] }}</h3>
        <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-3">{{ $card['count'] }}</p>
    </a>
@endforeach

        </div>
    </div>

    <!-- 🌗 Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') {
            htmlEl.classList.add('dark');
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>

    <!-- 🌙 Global Theme Styling -->
    <style>
        html {
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        body {
            background-color: #f9fafb;
        }

        .dark body {
            background-color: #0a1120;
        }

        /* Extra small screen adjustments */
        @media (max-width: 640px) {
            h2 {
                font-size: 1.25rem;
            }

            .p-6 {
                padding: 1.25rem;
            }
        }
    </style>
</x-app-layout>


// student controller

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
