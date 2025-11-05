<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
            {{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }} — Payment History
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        {{-- <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6"> --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Student Details</h3>
                <a href="{{ route('fee_payments.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    ← Back to Payments
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <p><strong>Name:</strong> {{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }}</p>
                    <p><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</p>
                </div>
                <div>
                    <p><strong>Total Fee:</strong> ₦{{ number_format($totalFees, 2) }}</p>
                    <p><strong>Total Paid:</strong> ₦{{ number_format($totalPaid, 2) }}</p>
                    <p><strong>Balance:</strong> ₦{{ number_format($balance, 2) }}</p>
                </div>
            </div>

            <h3 class="text-lg font-semibold mb-3">Payment History by Term/Session</h3>

            @php
                $groupedPayments = $payments->groupBy(function($item) {
                    return $item->term . '|' . $item->session;
                });
            @endphp

            @forelse ($groupedPayments as $termSession => $termPayments)
                @php
                    [$term, $session] = explode('|', $termSession);
                    $totalPaidTerm = $termPayments->sum('amount');
                    $totalFeeTerm = $termPayments->first()->fee->amount ?? 0;
                    $balanceTerm = $totalFeeTerm - $totalPaidTerm;
                @endphp

                <div class="mb-4 p-4 border rounded bg-gray-50 dark:bg-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <p><strong>Session:</strong> {{ $session }}</p>
                            <p><strong>Term:</strong> {{ $term }}</p>
                            <p><strong>Total Paid:</strong> ₦{{ number_format($totalPaidTerm, 2) }}</p>
                            <p><strong>Balance:</strong> ₦{{ number_format($balanceTerm, 2) }}</p>
                        </div>
                        <div>
                            <a href="{{ route('receipts.view', ['student' => $student->id, 'term' => $term, 'session' => $session]) }}"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Generate Receipt
                             </a>
                             
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                    <th class="px-4 py-2 text-left">Fee</th>
                                    <th class="px-4 py-2 text-left">Amount Paid</th>
                                    <th class="px-4 py-2 text-left">Balance After Payment</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($termPayments as $p)
                                    <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-2">{{ $p->fee->name ?? '—' }}</td>
                                        <td class="px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($p->amount, 2) }}</td>
                                        <td class="px-4 py-2 text-red-600 dark:text-red-400">₦{{ number_format($p->balance_after_payment, 2) }}</td>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-300">No payments recorded for this student yet.</p>
            @endforelse

            <div class="mt-6 text-right">
                <h4 class="text-lg font-semibold">Overall Summary</h4>
                <p><strong>Total Fee:</strong> ₦{{ number_format($totalFees, 2) }}</p>
                <p><strong>Total Paid:</strong> ₦{{ number_format($totalPaid, 2) }}</p>
                <p><strong>Outstanding Balance:</strong> ₦{{ number_format($balance, 2) }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
