<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Display the receipt for a single payment.
     */
    public function show($paymentId)
    {
        $payment = FeePayment::with(['student.schoolClass', 'fee', 'school'])
            ->findOrFail($paymentId);

        $student = $payment->student;
        $school  = $payment->school;

        // All payments by this student
        $paymentHistory = FeePayment::where('student_id', $student->id)
            ->orderBy('payment_date', 'asc')
            ->get();

        $totalPaid = $paymentHistory->sum('amount');
        $totalFee  = optional($payment->fee)->amount ?? 0;
        $balance   = max($totalFee - $totalPaid, 0);

        return view('receipts.show', compact(
            'payment', 'paymentHistory', 'student', 'school', 'totalFee', 'totalPaid', 'balance'
        ));
    }

    /**
     * Generate and download PDF for a single payment.
     */
    public function download($paymentId)
    {
        $payment = FeePayment::with(['student.schoolClass', 'fee', 'school'])
            ->findOrFail($paymentId);

        $student = $payment->student;
        $school  = $payment->school;

        $paymentHistory = FeePayment::where('student_id', $student->id)
            ->orderBy('payment_date', 'asc')
            ->get();

        $totalPaid = $paymentHistory->sum('amount');
        $totalFee  = optional($payment->fee)->amount ?? 0;
        $balance   = max($totalFee - $totalPaid, 0);

        $pdf = Pdf::loadView('receipts.pdf', compact(
            'payment', 'paymentHistory', 'student', 'school', 'totalFee', 'totalPaid', 'balance'
        ));

        $filename = 'Receipt_' . $student->name . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate receipt for a student for a given term and session.
     */
    public function view(Request $request)
{
    $studentId = $request->query('student');
    $term      = $request->query('term');
    $session   = $request->query('session');

    $student = Student::findOrFail($studentId);
    $school  = School::first(); // adjust if multi-tenant

    $payments = FeePayment::where('student_id', $studentId)
                  ->where('term', $term)
                  ->where('session', $session)
                  ->get();

    $totalPaid = $payments->sum('amount');
    $totalFees = $payments->sum(fn($p) => $p->fee->amount ?? 0);
    $balance   = $totalFees - $totalPaid;

    return view('receipts.show', compact('student', 'school', 'payments', 'term', 'session', 'totalPaid', 'totalFees', 'balance'));
}

}
