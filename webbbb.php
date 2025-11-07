<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | EduSmart</title>
  @vite('resources/css/app.css')
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
      animation: fadeIn 1s ease-out forwards;
    }
    .mobile-menu {
      transition: transform 0.3s ease-in-out;
      transform: translateX(-100%);
    }
    .mobile-menu.open {
      transform: translateX(0);
    }
  </style>
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans flex flex-col">

  <!-- 🔹 Navbar -->
  <nav class="bg-gray-900 bg-opacity-70 backdrop-blur-md border-b border-gray-800 px-6 py-4 flex justify-between items-center relative">
    <a href="/" class="text-blue-400 text-2xl font-bold">EduSmart</a>

    <!-- Desktop Links -->
    <div class="hidden md:flex space-x-6 text-gray-300 items-center">
      <a href="/" class="text-blue-400 font-semibold">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>

      @auth
        <a href="{{ url('/dashboard') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg ml-2">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a>
        <a href="{{ route('register') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg">Register</a>
      @endauth
    </div>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 left-0 w-64 h-full bg-gray-900 bg-opacity-95 backdrop-blur-md border-r border-gray-800 p-6 z-50">
      <button id="close-menu" class="text-gray-400 hover:text-white mb-8 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col space-y-4 text-gray-300">
        <a href="/" class="text-blue-400 font-semibold">Home</a>
        <a href="/features" class="hover:text-blue-400">Features</a>
        <a href="/about" class="hover:text-blue-400">About</a>
        <a href="/contact" class="hover:text-blue-400">Contact</a>
        <a href="/pricing" class="hover:text-blue-400">Pricing</a>

        @auth
          <a href="{{ url('/dashboard') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg text-center">Dashboard</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg w-full mt-2">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a>
          <a href="{{ route('register') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg text-center">Register</a>
        @endauth
      </nav>
    </div>
  </nav>

  <!-- 🔹 Hero Section -->
  <main class="flex-grow flex items-center justify-center px-6 fade-in">
    <div class="text-center space-y-6">
      <div class="flex justify-center mb-4">
        <div class="bg-blue-600 text-white font-bold text-3xl px-6 py-2 rounded-2xl shadow-lg">
          EduSmart
        </div>
      </div>
      <h1 class="text-3xl md:text-4xl font-semibold">
        Simplify School Management. <br>
        <span class="text-blue-400">Fast. Smart. Connected.</span>
      </h1>
      <p class="text-gray-400 text-base md:text-lg max-w-lg mx-auto">
        EduSmart helps schools manage students, results, and payments effortlessly — all in one place.
      </p>

      <div class="flex flex-col md:flex-row justify-center gap-4 mt-8">
        @auth
          <a href="{{ url('/dashboard') }}"
             class="bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-3 px-8 rounded-full">
             Go to Dashboard
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="border border-red-600 text-red-400 hover:bg-red-600 hover:text-white transition font-semibold py-3 px-8 rounded-full">
              Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-3 px-8 rounded-full">
             Login
          </a>
          <a href="{{ route('register') }}"
             class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white transition font-semibold py-3 px-8 rounded-full">
             Register
          </a>
        @endauth
      </div>
    </div>
  </main>

  <footer class="text-gray-500 text-sm text-center py-6 border-t border-gray-800">
    &copy; {{ date('Y') }} EduSmart · Powered by Sawo Software Systems
  </footer>

  <script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenu = document.getElementById('close-menu');

    menuBtn.addEventListener('click', () => mobileMenu.classList.add('open'));
    closeMenu.addEventListener('click', () => mobileMenu.classList.remove('open'));
  </script>

</body>
</html>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- ✅ Scripts & Styles: Vite for local, Build for production --}}
    @php
    $manifestPath = public_path('build/manifest.json');
    $isLocal = app()->environment('local');
@endphp

@if (file_exists($manifestPath))
    {{-- ✅ Production Build: load compiled assets --}}
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $appCss = $manifest['resources/css/app.css']['file'] ?? null;
        $appJs = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp

    @if ($appCss)
        <link rel="stylesheet" href="{{ asset('build/' . $appCss) }}">
    @endif
    @if ($appJs)
        <script src="{{ asset('build/' . $appJs) }}" defer></script>
    @endif

@elseif ($isLocal)
    {{-- 🖥️ Local Development: use Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

@else
    {{-- 🔥 Fallback if no build or wrong env --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
@endif


</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">

    <div class="min-h-screen flex flex-col">

        {{-- Page Heading --}}
        @if (isset($header))
            <header class="bg-white shadow-sm border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 max-w-7xl mx-auto w-full py-8 px-4 sm:px-6 lg:px-8 pb-32">
            {{ $slot }}
        </main>
    </div>

    {{-- 🔙 Back to Dashboard --}}
    @auth
        <a href="{{ route('dashboard') }}"
           class="fixed bottom-20 left-6 inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            ⬅️ Back to Dashboard
        </a>
    @endauth

    {{-- 🏠 Home Button --}}
    <a href="{{ url('/') }}"
       class="fixed bottom-6 left-6 inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-gray-700 rounded-full shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
        🏠 Home
    </a>

    {{-- 🌙 Dark Mode Toggle --}}
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 p-3 rounded-full bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 shadow-md transition">
        🌙
    </button>

    {{-- 🌗 Dark Mode Script --}}
    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById("theme-toggle");

        if (localStorage.getItem("theme") === "dark") {
            html.classList.add("dark");
        }

        themeToggle.addEventListener("click", () => {
            html.classList.toggle("dark");
            localStorage.setItem("theme", html.classList.contains("dark") ? "dark" : "light");
        });
    </script>

</body>
</html>


<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}


// class controller

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


// class model

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes'; // since pluralized name is "classes"

    
    protected $fillable = [
        'name', 'section', 'form_teacher_id', 'next_class_id'
    ];
    

    // 🔹 Class has many students
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // 🔹 Class belongs to a form teacher
    public function formTeacher()
    {
        return $this->belongsTo(Teacher::class, 'form_teacher_id');
        
    }
    

    // 🔹 Class has many subjects, taught by different teachers
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_teacher',
            'class_id',
            'subject_id'
        )->withPivot('teacher_id');
    }
    public function nextClass()
    {
        return $this->belongsTo(SchoolClass::class, 'next_class_id');
    }

    public function previousClasses()
    {
        return $this->hasMany(SchoolClass::class, 'next_class_id');
    }
    public function fees()
{
    return $this->hasMany(Fee::class, 'class_id');
}

public function school()
{
    return $this->belongsTo(School::class);
}

// app/Models/Classroom.php
public function student()
{
    return $this->hasMany(Student::class);
}


}


// class show blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Class Details
            </h2>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 text-gray-900 dark:text-gray-100 transition">

                <!-- Class Info -->
                <h3 class="text-lg font-semibold mb-4 border-l-4 border-blue-500 pl-2">Class Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div><strong>Class Name:</strong> {{ $class->name }}</div>
                    <div><strong>Section:</strong> {{ $class->section ?? '-' }}</div>
                    <div><strong>Assigned Teacher:</strong> {{ $class->formTeacher->user->name ?? 'Unassigned' }}</div>
                    <div></div>
                </div>

                @if($students->count() === 0)
                    <p class="text-gray-500 dark:text-gray-400">No students enrolled in this class.</p>
                @else
                    @php
                        $latestFee = $class->fees->max('amount') ?? 0;
                    @endphp

                    <!-- Hidden data template: each child div holds dataset for each student -->
                    <div id="students-data" class="hidden">
                        @foreach($students as $student)
                            @php
                                $totalPaid = $student->feePayments->sum('amount');
                                $balance = max($latestFee - $totalPaid, 0);
                                $lastPayment = $student->feePayments->sortByDesc('created_at')->first();
                                $lastDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : null;
                            @endphp
                            <div
                                class="student-item"
                                data-id="{{ $student->id }}"
                                data-name="{{ e($student->name) }}"
                                data-adm="{{ e($student->admission_number) }}"
                                data-fee="{{ $latestFee }}"
                                data-paid="{{ $totalPaid }}"
                                data-balance="{{ $balance }}"
                                data-last="{{ $lastDate ?? '' }}"
                                data-view="{{ route('students.show', $student->id) }}"
                                data-edit="{{ route('students.edit', $student->id) }}"
                                data-delete="{{ route('students.destroy', $student->id) }}"
                            ></div>
                        @endforeach
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-sm" id="students-table">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th class="px-4 py-2 text-left">Student Name</th>
                                    <th class="px-4 py-2 text-left">Admission No</th>
                                    <th class="px-4 py-2 text-left">Total Fee (₦)</th>
                                    <th class="px-4 py-2 text-left">Total Paid (₦)</th>
                                    <th class="px-4 py-2 text-left">Balance (₦)</th>
                                    <th class="px-4 py-2 text-left">Last Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @php
                                        $totalPaid = $student->feePayments->sum('amount');
                                        $balance = max($latestFee - $totalPaid, 0);
                                        $lastPayment = $student->feePayments->sortByDesc('created_at')->first();
                                        $lastDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : '—';
                                    @endphp
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $student->name }}</td>
                                        <td class="px-4 py-2">{{ $student->admission_number }}</td>
                                        <td class="px-4 py-2">₦{{ number_format($latestFee, 2) }}</td>
                                        <td class="px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($totalPaid, 2) }}</td>
                                        <td class="px-4 py-2 {{ $balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            ₦{{ number_format($balance, 2) }}
                                        </td>
                                        <td class="px-4 py-2">{{ $lastDate }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        
                        
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Edit
                        </a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this class?')">
                                Delete
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('classes.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


// class controller

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
        // Load class with students, feePayments, formTeacher, and fees
        $class = SchoolClass::with([
            'students.feePayments' => fn($q) => $q->orderByDesc('payment_date'),
            'formTeacher.user',
            'fees'
        ])->findOrFail($id);
    
        $latestFee = $class->fees->max('amount') ?? 0;
    
        // Transform students
        $students = $class->students->transform(function ($student) use ($latestFee) {
            $totalPaid = $student->feePayments->sum('amount');
            $balance = max($latestFee - $totalPaid, 0);
            $lastPayment = $student->feePayments->first();
    
            $student->total_fee = $latestFee;
            $student->total_paid = $totalPaid;
            $student->balance = $balance;
            $student->last_payment_date = $lastPayment ? $lastPayment->payment_date : null;
    
            return $student;
        });
    
        return view('classes.show', [
            'class' => $class,
            'students' => $students,   // ✅ pass this explicitly
            'latestFee' => $latestFee,
        ]);
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


// show blade file fully working

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Class Details
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 text-gray-900 dark:text-gray-100 transition">

                <!-- Class Info -->
                <h3 class="text-lg font-semibold mb-4 border-l-4 border-blue-500 pl-2">Class Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div><strong>Class Name:</strong> {{ $class->name }}</div>
                    <div><strong>Section:</strong> {{ $class->section ?? '-' }}</div>
                    <div><strong>Assigned Teacher:</strong> {{ $class->formTeacher->user->name ?? 'Unassigned' }}</div>
                    <div></div>
                </div>

                <!-- Fee Filter -->
                <form method="GET" class="mb-4">
                    <label for="fee_status" class="text-sm font-medium mr-2">Filter by Fees:</label>
                    <select name="fee_status" id="fee_status" onchange="this.form.submit()"
                            class="px-3 py-1 border rounded dark:bg-gray-700 dark:text-gray-100">
                        <option value="all" {{ $feeFilter === 'all' ? 'selected' : '' }}>All Students</option>
                        <option value="fully-paid" {{ $feeFilter === 'fully-paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ $feeFilter === 'partial' ? 'selected' : '' }}>Partial Payment</option>
                        <option value="unpaid" {{ $feeFilter === 'unpaid' ? 'selected' : '' }}>Not Paid</option>
                    </select>
                </form>

                @if($students->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No students found.</p>
                @else
                    @php
                        $totalFee = $students->sum('total_fee');
                        $totalPaid = $students->sum('total_paid');
                        $totalBalance = $students->sum('balance');
                    @endphp

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th class="px-4 py-2 text-left">Student Name</th>
                                    <th class="px-4 py-2 text-left">Admission No</th>
                                    <th class="px-4 py-2 text-left">Total Fee (₦)</th>
                                    <th class="px-4 py-2 text-left">Total Paid (₦)</th>
                                    <th class="px-4 py-2 text-left">Balance (₦)</th>
                                    <th class="px-4 py-2 text-left">Last Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($students as $student)
                                @php
                                    $totalPaid = $student->feePayments->sum('amount');
                                    $balance = max($latestFee - $totalPaid, 0);
                                    $lastPayment = $student->feePayments->sortByDesc('created_at')->first();
                                    $lastDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : '—';
                                @endphp
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $student->name }}</td>
                                    <td class="px-4 py-2">{{ $student->admission_number }}</td>
                                    <td class="px-4 py-2">₦{{ number_format($latestFee, 2) }}</td>
                                    <td class="px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($totalPaid, 2) }}</td>
                                    <td class="px-4 py-2 {{ $balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        ₦{{ number_format($balance, 2) }}
                                    </td>
                                    <td class="px-4 py-2">{{ $lastDate }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 dark:bg-gray-800 font-semibold">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right">Class Totals:</td>
                                    <td class="px-4 py-2">₦{{ number_format($totalFee, 2) }}</td>
                                    <td class="px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($totalPaid, 2) }}</td>
                                    <td class="px-4 py-2 text-red-600 dark:text-red-400">₦{{ number_format($totalBalance, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @foreach($students as $student)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-blue-700 dark:text-blue-300">{{ $student->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Admission: {{ $student->admission_number }}</p>
                                    </div>
                                    <div class="text-right text-sm">
                                        <p class="{{ $student->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} font-semibold">
                                            {{ $student->balance > 0 ? 'Owing' : 'Paid' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                    <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Fee</span><br>₦{{ number_format($student->total_fee, 2) }}</div>
                                    <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Paid</span><br><span class="text-green-600 dark:text-green-400">₦{{ number_format($student->total_paid, 2) }}</span></div>
                                    <div class="text-gray-500 dark:text-gray-300"><span class="font-medium">Balance</span><br><span class="{{ $student->balance>0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">₦{{ number_format($student->balance, 2) }}</span></div>
                                </div>

                                <div class="mt-3 flex items-center justify-between text-sm text-gray-500 dark:text-gray-300">
                                    Last: {{ $student->last_payment_date?->format('Y-m-d') ?? '—' }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Edit
                        </a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this class?')">
                                Delete
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('classes.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
