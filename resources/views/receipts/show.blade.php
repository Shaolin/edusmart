<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">
            Receipt — {{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }}
        </h2>
        <p class="text-blue-600 dark:text-blue-400 font-semibold">{{ $term }} | {{ $session }}</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden relative p-6">

            {{-- Watermark --}}
            @if($school && $school->logo)
                {{-- <img src="{{ asset('storage/' . $school->logo) }}"
                     class="absolute top-1/2 left-1/2 w-72 opacity-5 transform -translate-x-1/2 -translate-y-1/2 rotate-12 z-0"> --}}
                     <img src="{{ asset('school_logos/' . $school->logo) }}"
                     class="absolute top-1/2 left-1/2 w-72 opacity-5 transform -translate-x-1/2 -translate-y-1/2 rotate-12 z-0">
                
            @endif

            {{-- Header --}}
            <div class="flex justify-between items-center mb-6 relative z-10">
                <div class="flex items-center">
                    @if($school && $school->logo)
                        {{-- <img src="{{ asset('storage/' . $school->logo) }}" class="w-20 h-20 object-contain mr-4"> --}}
                        <img src="{{ asset('school_logos/' . $school->logo) }}" class="w-20 h-20 object-contain mr-4">

                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-blue-800 dark:text-blue-400">{{ $school->name ?? 'School Name' }}</h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ $school->address ?? '' }}</p>
                        <p class="text-gray-600 dark:text-gray-300">
                            Contact: <span class="text-blue-600 dark:text-blue-400">{{ $school->phone ?? $school->contact ?? 'Not set' }}</span>
                        </p>
                        @if($school->email)
                            <p>Email: <span class="text-purple-600 dark:text-purple-400">{{ $school->email }}</span></p>
                        @endif
                        @if($school->website)
                            <p>Website: <span class="text-purple-600 dark:text-purple-400">{{ $school->website }}</span></p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-yellow-200 dark:bg-yellow-700 rounded font-semibold text-sm text-yellow-800 dark:text-yellow-100">
                        Receipt
                    </span>
                </div>
            </div>

            {{-- Student Info --}}
            <div class="mb-6 relative z-10 border-b border-gray-300 dark:border-gray-700 pb-4">
                <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">Student Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <p><strong>Name:</strong> <span class="text-blue-600 dark:text-blue-400">{{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }}</span></p>
                    <p><strong>Class:</strong> <span class="text-green-600 dark:text-green-400">{{ $student->schoolClass->name ?? '—' }}</span></p>
                    <p><strong>Session:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $session }}</span></p>
                    <p><strong>Term:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $term }}</span></p>
                </div>
            </div>

            {{-- Payment Table --}}
            <div class="overflow-x-auto relative z-10">
                <table class="min-w-full border-collapse border border-gray-300 dark:border-gray-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="border px-4 py-2 text-left">Fee</th>
                            <th class="border px-4 py-2 text-left">Amount Paid (₦)</th>
                            <th class="border px-4 py-2 text-left">Balance After Payment (₦)</th>
                            <th class="border px-4 py-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPaid = 0;
                            $totalBalance = 0;
                        @endphp
                        @forelse($payments as $payment)
                            @php
                                $totalPaid += $payment->amount;
                                $totalBalance = $payment->balance_after_payment;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 text-indigo-600 dark:text-indigo-400">{{ $payment->fee->name ?? '—' }}</td>
                                <td class="border px-4 py-2 text-green-600 dark:text-green-400">₦{{ number_format($payment->amount, 2) }}</td>
                                <td class="border px-4 py-2 text-red-600 dark:text-red-400">₦{{ number_format($payment->balance_after_payment, 2) }}</td>
                                <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                                    No payments recorded for this student yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
                        <tr>
                            <td class="px-4 py-2 text-right">Totals:</td>
                            <td class="px-4 py-2 text-green-700 dark:text-green-400">₦{{ number_format($totalPaid, 2) }}</td>
                            <td class="px-4 py-2 text-red-700 dark:text-red-400">₦{{ number_format($totalBalance, 2) }}</td>
                            <td class="px-4 py-2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Footer --}}
            <div class="mt-6 text-right relative z-10">
                <p class="text-blue-600 dark:text-blue-400 font-semibold">Thank you for your payment!</p>
            </div>

 {{-- Action Buttons --}}
<div class="mt-6 flex flex-col sm:flex-row sm:flex-wrap gap-3 no-print">
    <a href="{{ route('students.fees.download', [$student->id, 'session' => request('session'), 'term' => request('term')]) }}"
       class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded text-center flex-1 sm:flex-none">
        📥 Download PDF
    </a>
   

    <a href="{{ route('students.fees.sendWhatsapp', $student->id) }}"
        target="_blank"
        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
         📄 Send Receipt
     </a>
     
     
</div>


        </div>
    </div>
</x-app-layout>
