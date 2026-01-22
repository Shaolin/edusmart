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


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2
                       text-gray-900 dark:text-gray-100 transition-colors duration-300">
                Class Details
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6
                        text-gray-900 dark:text-gray-100 transition">

                {{-- ================= Class Info ================= --}}
                <h3 class="text-lg font-semibold mb-4 border-l-4 border-blue-500 pl-2">
                    Class Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div><strong>Class Name:</strong> {{ $class->name }}</div>
                    <div><strong>Section:</strong> {{ $class->section ?? '-' }}</div>
                    <div>
                        <strong>Assigned Teacher:</strong>
                        {{ $class->formTeacher->user->name ?? 'Unassigned' }}
                    </div>
                </div>

                {{-- ================= Filters ================= --}}
                <form method="GET" class="mb-4 flex flex-wrap items-center gap-4">
                    <div>
                        <label class="text-sm font-medium">Select Term:</label>
                        <select name="term" onchange="this.form.submit()"
                                class="px-3 py-1 border rounded dark:bg-gray-700">
                            @foreach(['first','second','third'] as $termOption)
                                <option value="{{ $termOption }}"
                                    {{ $selectedTerm === $termOption ? 'selected' : '' }}>
                                    {{ ucfirst($termOption) }} Term
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Filter by Fees:</label>
                        <select name="fee_status" onchange="this.form.submit()"
                                class="px-3 py-1 border rounded dark:bg-gray-700">
                            <option value="all" {{ $feeFilter === 'all' ? 'selected' : '' }}>
                                All Students
                            </option>
                            <option value="fully-paid" {{ $feeFilter === 'fully-paid' ? 'selected' : '' }}>
                                Fully Paid
                            </option>
                            <option value="partial" {{ $feeFilter === 'partial' ? 'selected' : '' }}>
                                Partial Payment
                            </option>
                            <option value="unpaid" {{ $feeFilter === 'unpaid' ? 'selected' : '' }}>
                                Not Paid
                            </option>
                        </select>
                    </div>
                </form>

                {{-- ================= Students Table ================= --}}
                @if($students->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No students found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
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
                                        $totalPaid = $student->total_paid ?? 0;
                                        $balance   = max($latestFee - $totalPaid, 0);
                                        $lastDate  = optional(
                                            $student->feePayments->first()
                                        )->created_at?->format('Y-m-d') ?? '—';
                                    @endphp

                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $student->name }}</td>
                                        <td class="px-4 py-2">{{ $student->admission_number }}</td>
                                        <td class="px-4 py-2">
                                            ₦{{ number_format($latestFee, 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-green-600 dark:text-green-400">
                                            ₦{{ number_format($totalPaid, 2) }}
                                        </td>
                                        <td class="px-4 py-2
                                            {{ $balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            ₦{{ number_format($balance, 2) }}
                                        </td>
                                        <td class="px-4 py-2">{{ $lastDate }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- ================= Class Totals ================= --}}
                            <tfoot class="bg-gray-100 dark:bg-gray-800 font-semibold">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right">
                                        Class Totals:
                                    </td>
                                    <td class="px-4 py-2">
                                        ₦{{ number_format($totalFee, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-green-600 dark:text-green-400">
                                        ₦{{ number_format($totalPaidSum, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-red-600 dark:text-red-400">
                                        ₦{{ number_format($totalBalance, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif

                {{-- ================= Actions ================= --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}"
                           class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Edit
                        </a>

                        <form action="{{ route('classes.destroy', $class->id) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('classes.index') }}"
                       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>



// the working blade and controller

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

        $class = SchoolClass::with(['formTeacher.user', 'fees'])
                    ->where('school_id', $user->school_id)
                    ->findOrFail($id);

        $feeFilter = $request->query('fee_status', 'all');
        $latestFee = $class->fees->max('amount') ?? 0;

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

        $students = $studentsQuery->paginate(20)->withQueryString();

        return view('classes.show', compact('class', 'students', 'latestFee', 'feeFilter'));
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
            'name'            => 'required|string|max:255|unique:classes,name',
            'section'         => 'nullable|string|max:255',
            'form_teacher_id' => 'nullable|exists:teachers,id',
            'next_class_id'   => 'nullable|exists:school_classes,id',
        ]);

        // Assign school_id automatically
        $validated['school_id'] = $user->school_id;

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
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

                <!-- Fee Filter -->
                <form method="GET" class="mb-4">
                    <label for="fee_status" class="text-sm font-medium">Filter by Fees:</label>
                    <select name="fee_status" id="fee_status" onchange="this.form.submit()"
                            class="px-3 py-1 border rounded dark:bg-gray-700 dark:text-gray-100">
                        <option value="all" {{ $feeFilter === 'all' ? 'selected' : '' }}>All Students</option>
                        <option value="fully-paid" {{ $feeFilter === 'fully-paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ $feeFilter === 'partial' ? 'selected' : '' }}>Partial Payment</option>
                        <option value="unpaid" {{ $feeFilter === 'unpaid' ? 'selected' : '' }}>Not Paid</option>
                    </select>
                </form>

                @php
                    $latestFee = $class->fees->max('amount') ?? 0;
                    $totalFee = 0;
                    $totalPaidSum = 0;
                    $totalBalance = 0;
                @endphp

                @if($students->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No students found.</p>
                @else
                    <div class="overflow-x-auto">
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

                                        // Accumulate totals
                                        $totalFee += $latestFee;
                                        $totalPaidSum += $totalPaid;
                                        $totalBalance += $balance;
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
                                    <td class="px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($totalPaidSum, 2) }}</td>
                                    <td class="px-4 py-2 text-red-600 dark:text-red-400">₦{{ number_format($totalBalance, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination (if using Laravel pagination for $students) -->
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
