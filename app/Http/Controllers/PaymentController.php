<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['student', 'fee.schoolClass'])
            ->latest()
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $fees = Fee::with('schoolClass')->get();

        return view('payments.create', compact('students', 'fees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_id' => 'required|exists:fees,id',
            'amount_paid' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $fee = Fee::findOrFail($request->fee_id);
        $totalPaid = Payment::where('student_id', $request->student_id)
                            ->where('fee_id', $request->fee_id)
                            ->sum('amount_paid');

        $newTotal = $totalPaid + $request->amount_paid;

        $status = $newTotal >= $fee->amount ? 'paid' : 'partial';

        Payment::create([
            'student_id' => $request->student_id,
            'fee_id' => $request->fee_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => now(),
            'method' => $request->method,
            'status' => $status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $students = Student::orderBy('name')->get();
        $fees = Fee::with('schoolClass')->get();
        return view('payments.edit', compact('payment', 'students', 'fees'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->only('amount_paid', 'method', 'notes'));

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
