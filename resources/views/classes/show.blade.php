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
                    <select name="term" onchange="this.form.submit()" class="px-3 py-1 border rounded dark:bg-gray-700">
                        <option value="first" {{ $uiTerm === 'first' ? 'selected' : '' }}>First Term</option>
                        <option value="second" {{ $uiTerm === 'second' ? 'selected' : '' }}>Second Term</option>
                        <option value="third" {{ $uiTerm === 'third' ? 'selected' : '' }}>Third Term</option>
                    </select>

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
                    // ✅ Active fee per term/session
                    $activeFee = $class->fees
                        ->where('term', $uiTerm)  // fees table uses first/second/third
                        ->where('session', $activeSession)
                        ->first();

                    $latestFee = $activeFee?->amount ?? 0;

                    // Initialize totals
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
                                        // ✅ Map UI term to feePayments term
                                        $termMap = [
                                            'first'  => 'First Term',
                                            'second' => 'Second Term',
                                            'third'  => 'Third Term',
                                        ];
                                        $paymentTerm = $termMap[$uiTerm] ?? 'First Term';

                                        // Filter payments for this term/session
                                        $termPayments = $student->feePayments
                                            ->where('term', $paymentTerm)
                                            ->where('session', $activeSession);

                                        $totalPaid = $termPayments->sum('amount');
                                        $balance   = max($latestFee - $totalPaid, 0);

                                        $lastPayment = $termPayments->sortByDesc('created_at')->first();
                                        $lastDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : '—';

                                        // Accumulate class totals
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

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('classes.edit', $class->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Edit</a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                        </form>
                    @endif
                    <a href="{{ route('classes.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back to List</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
