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
     * Restrict access to admins only
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display all fee payments (with search)
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();
    
        $search = $request->input('student_name');
    
        $payments = FeePayment::with(['student.schoolClass', 'fee'])
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('student.schoolClass', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('session', 'like', "%{$search}%");
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(10);
    
        return view('fee_payments.index', compact('payments', 'search'));
    }
    

    /**
     * Show the form to record a new payment
     */
    public function create()
    {
        $this->authorizeAdmin();

        $classes = SchoolClass::all();
        $fees = Fee::with('schoolClass')->get();

        return view('fee_payments.create', compact('classes', 'fees'));
    }

    /**
     * AJAX: Get students belonging to a specific class
     */
    public function getStudentsByClass($classId)
    {
        $students = Student::where('class_id', $classId)->get();

        return response()->json($students);
    }

    /**
     * Store a new payment record
     */
    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'fee_id' => 'required|exists:fees,id',
        'amount' => 'required|numeric|min:1',
        'session' => 'required|string',
        'term' => 'required|string',
        'payment_date' => 'required|date',
    ]);

    $fee = Fee::findOrFail($request->fee_id);

    // Calculate total amount paid so far for this student and fee
    $totalPaid = FeePayment::where('student_id', $request->student_id)
        ->where('fee_id', $request->fee_id)
        ->sum('amount');

    // Compute new balance
    $balance = $fee->amount - ($totalPaid + $request->amount);

    // Prevent overpayment
    if ($balance < 0) {
        return back()->withErrors(['amount' => 'Amount exceeds total fee for this student.']);
    }

    // Save payment
    FeePayment::create([
        'student_id' => $request->student_id,
        'fee_id' => $request->fee_id,
        'amount' => $request->amount,
        'session' => $request->session,
        'term' => $request->term,
        'payment_date' => $request->payment_date,
        'balance_after_payment' => $balance,
    ]);

    return redirect()->route('fee_payments.index')
        ->with('success', 'Payment recorded successfully!');
}


    /**
     * Show all payments made by a particular student
     */
    public function show($studentId)
    {
        $this->authorizeAdmin();
    
        $student = Student::with('schoolClass')->findOrFail($studentId);
    
        // Get all payments made by the student
        $payments = FeePayment::with('fee.schoolClass')
            ->where('student_id', $studentId)
            ->orderBy('payment_date', 'desc')
            ->get();
    
        // Get all unique fees the student has paid for (to avoid counting same fee multiple times)
        $feeIds = $payments->pluck('fee_id')->unique();
    
        // Sum only the original fee amounts once per fee type
        $totalFees = Fee::whereIn('id', $feeIds)->sum('amount');
    
        // Total paid so far (sum of all partial payments)
        $totalPaid = $payments->sum('amount');
    
        // Remaining balance (never less than 0)
        $balance = max($totalFees - $totalPaid, 0);
    
        return view('fee_payments.show', compact('student', 'payments', 'totalFees', 'totalPaid', 'balance'));
    }
    
    /**
 * Show the form for editing a specific payment
 */
public function edit(FeePayment $feePayment)
{
    $this->authorizeAdmin();

    $feePayment->load(['student.schoolClass', 'fee']);

    return view('fee_payments.edit', [
        'payment' => $feePayment
    ]);
}

/**
 * Update a specific payment
 */
public function update(Request $request, FeePayment $feePayment)
{
    $this->authorizeAdmin();

    $request->validate([
        'amount' => 'required|numeric|min:1',
        'session' => 'required|string',
        'term' => 'required|string',
    ]);

    $fee = Fee::findOrFail($feePayment->fee_id);

    // Calculate total amount already paid (excluding this record)
    $totalPaidBefore = FeePayment::where('student_id', $feePayment->student_id)
        ->where('fee_id', $feePayment->fee_id)
        ->where('id', '!=', $feePayment->id)
        ->sum('amount');

    // Compute new total and balance
    $newTotalPaid = $totalPaidBefore + $request->amount;
    $newBalance = $fee->amount - $newTotalPaid;

    if ($newBalance < 0) {
        return back()->withErrors(['amount' => 'Amount exceeds total fee for this student.']);
    }

    // Update record
    $feePayment->update([
        'amount' => $request->amount,
        'session' => $request->session,
        'term' => $request->term,
        'balance_after_payment' => $newBalance,
    ]);

    return redirect()
        ->route('fee_payments.index')
        ->with('success', 'Payment updated successfully!');
}


    /**
     * Delete a payment record
     */
    public function destroy(FeePayment $feePayment)
    {
        $this->authorizeAdmin();

        $feePayment->delete();

        return redirect()
            ->route('fee_payments.index')
            ->with('success', 'Payment deleted successfully!');
    }
}
