<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Payments</h1>

        <a href="{{ route('payments.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Record Payment</a>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-2 mt-3 rounded">{{ session('success') }}</div>
        @endif

        <table class="w-full mt-4 border-collapse border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Student</th>
                    <th class="border px-4 py-2">Class</th>
                    <th class="border px-4 py-2">Fee Name</th>
                    <th class="border px-4 py-2">Amount Paid</th>
                    <th class="border px-4 py-2">Date</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2">{{ $payment->student->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $payment->fee->schoolClass->name ?? 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $payment->fee->name }}</td>
                    <td class="border px-4 py-2">₦{{ number_format($payment->amount_paid, 2) }}</td>
                    <td class="border px-4 py-2">{{ $payment->payment_date }}</td>
                    <td class="border px-4 py-2">
                        @if($payment->status === 'paid')
                            <span class="text-green-600 font-semibold">Paid</span>
                        @else
                            <span class="text-yellow-600 font-semibold">Partial</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('payments.edit', $payment) }}" class="text-blue-600">Edit</a> |
                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this payment?')" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</x-app-layout>
