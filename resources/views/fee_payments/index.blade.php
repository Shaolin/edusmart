<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
          
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                All Fee Payments
             </h2>
           
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-6 transition-colors duration-300 text-gray-900 dark:text-gray-100">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <h3 class="text-lg font-semibold">Payments Overview</h3>
                <a href="{{ route('fee_payments.create') }}"
                   class="bg-blue-600 w-full sm:w-auto text-center text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Record Payment
                </a>
            </div>

            {{-- Search --}}
            <div class="mb-4">
                <form action="{{ route('fee_payments.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <input type="text"
                           name="student_name"
                           placeholder="Search by student, class, or session"
                           value="{{ request('student_name') }}"
                           class="border-gray-300 rounded-lg w-full px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-black" />
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full sm:w-auto">
                        Search
                    </button>
                </form>
            </div>

            {{-- Success message --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Desktop/Tablet Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Student</th>
                            <th class="px-4 py-2 text-left">Class</th>
                            <th class="px-4 py-2 text-left">Fee</th>
                            <th class="px-4 py-2 text-left">Amount Paid</th>
                            <th class="px-4 py-2 text-left">Balance After</th>
                            <th class="px-4 py-2 text-left">Session</th>
                            <th class="px-4 py-2 text-left">Term</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $row = 1; @endphp
                        @forelse ($payments as $payment)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2">{{ $row++ }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('fee_payments.show', $payment->student_id) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $payment->student->name ?? ($payment->student->first_name . ' ' . $payment->student->last_name) }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $payment->student->schoolClass->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $payment->fee->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-green-600">₦{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-4 py-2 text-red-600">₦{{ number_format($payment->balance_after_payment ?? 0, 2) }}</td>
                                <td class="px-4 py-2">{{ $payment->session }}</td>
                                <td class="px-4 py-2">{{ ucfirst($payment->term) }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 text-center space-x-2">
                                    <a href="{{ route('fee_payments.edit', $payment->id) }}"
                                       class="text-blue-600 hover:underline">Edit</a>

                                    <form action="{{ route('fee_payments.destroy', $payment->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                                    No payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="space-y-4 md:hidden">
                @forelse ($payments as $payment)
                    <div class="border dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-blue-600">
                                {{ $payment->student->name ?? ($payment->student->first_name . ' ' . $payment->student->last_name) }}
                            </h4>
                            <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">
                                {{ ucfirst($payment->term) }} • {{ $payment->session }}
                            </span>
                        </div>

                        <p><strong>Class:</strong> {{ $payment->student->schoolClass->name ?? '—' }}</p>
                        <p><strong>Fee:</strong> {{ $payment->fee->name ?? '—' }}</p>
                        <p><strong>Amount:</strong> ₦{{ number_format($payment->amount, 2) }}</p>
                        <p><strong>Balance After:</strong> ₦{{ number_format($payment->balance_after_payment ?? 0, 2) }}</p>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</p>

                        <div class="flex justify-end gap-4 mt-3">
                            <a href="{{ route('fee_payments.edit', $payment->id) }}" class="text-blue-600 text-sm">Edit</a>

                            <form action="{{ route('fee_payments.destroy', $payment->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 text-sm" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-300">No payments found.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $payments->withQueryString()->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
