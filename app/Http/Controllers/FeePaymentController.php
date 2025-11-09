<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeePaymentController extends Controller
{
    /**
     * Display all fee payments (admin only)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('student_name');

        $payments = FeePayment::with(['student.schoolClass', 'fee'])
            ->whereHas('student', fn($q) => $q->where('school_id', $user->school_id))
            ->when($search, function ($query, $search) {
                $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('student.schoolClass', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhere('session', 'like', "%{$search}%");
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return view('fee_payments.index', compact('payments', 'search'));
    }

    /**
     * Show form to record a new payment
     */
    public function create()
    {
        $user = Auth::user();

        $classes = SchoolClass::where('school_id', $user->school_id)->get();
        $fees = Fee::where('school_id', $user->school_id)->with('schoolClass')->get();

        return view('fee_payments.create', compact('classes', 'fees'));
    }

    /**
     * AJAX: Get students for a class
     */
    public function getStudentsByClass($classId)
    {
        $user = Auth::user();

        $students = Student::where('class_id', $classId)
            ->where('school_id', $user->school_id)
            ->get();

        return response()->json($students);
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_id'     => 'required|exists:fees,id',
            'amount'     => 'required|numeric|min:1',
            'session'    => 'required|string',
            'term'       => 'required|string',
            'payment_date' => 'required|date',
        ]);

        $student = Student::where('id', $request->student_id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $fee = Fee::where('id', $request->fee_id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $totalPaid = FeePayment::where('student_id', $student->id)
            ->where('fee_id', $fee->id)
            ->sum('amount');

        $balance = $fee->amount - ($totalPaid + $request->amount);

        if ($balance < 0) {
            return back()->withErrors(['amount' => 'Amount exceeds total fee for this student.']);
        }

        FeePayment::create([
            'student_id' => $student->id,
            'fee_id'     => $fee->id,
            'amount'     => $request->amount,
            'session'    => $request->session,
            'term'       => $request->term,
            'payment_date' => $request->payment_date,
            'balance_after_payment' => $balance,
        ]);

        return redirect()->route('fee_payments.index')
                         ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Show payments for a specific student
     */
    public function show($studentId)
    {
        $user = Auth::user();

        $student = Student::with('schoolClass')
            ->where('id', $studentId)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $payments = FeePayment::with('fee.schoolClass')
            ->where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        $feeIds = $payments->pluck('fee_id')->unique();
        $totalFees = Fee::whereIn('id', $feeIds)->where('school_id', $user->school_id)->sum('amount');
        $totalPaid = $payments->sum('amount');
        $balance = max($totalFees - $totalPaid, 0);

        return view('fee_payments.show', compact('student', 'payments', 'totalFees', 'totalPaid', 'balance'));
    }

    /**
     * Edit a payment
     */
    public function edit(FeePayment $feePayment)
    {
        $user = Auth::user();

        if ($feePayment->student->school_id !== $user->school_id || $feePayment->fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized');
        }

        $feePayment->load(['student.schoolClass', 'fee']);

        return view('fee_payments.edit', ['payment' => $feePayment]);
    }

    /**
     * Update a payment
     */
    public function update(Request $request, FeePayment $feePayment)
    {
        $user = Auth::user();

        if ($feePayment->student->school_id !== $user->school_id || $feePayment->fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'amount'  => 'required|numeric|min:1',
            'session' => 'required|string',
            'term'    => 'required|string',
        ]);

        $fee = $feePayment->fee;
        $totalPaidBefore = FeePayment::where('student_id', $feePayment->student_id)
            ->where('fee_id', $feePayment->fee_id)
            ->where('id', '!=', $feePayment->id)
            ->sum('amount');

        $newBalance = $fee->amount - ($totalPaidBefore + $request->amount);

        if ($newBalance < 0) {
            return back()->withErrors(['amount' => 'Amount exceeds total fee for this student.']);
        }

        $feePayment->update([
            'amount' => $request->amount,
            'session' => $request->session,
            'term' => $request->term,
            'balance_after_payment' => $newBalance,
        ]);

        return redirect()->route('fee_payments.index')
                         ->with('success', 'Payment updated successfully!');
    }

    /**
     * Delete a payment
     */
    public function destroy(FeePayment $feePayment)
    {
        $user = Auth::user();

        if ($feePayment->student->school_id !== $user->school_id || $feePayment->fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized');
        }

        $feePayment->delete();

        return redirect()->route('fee_payments.index')
                         ->with('success', 'Payment deleted successfully!');
    }
}
