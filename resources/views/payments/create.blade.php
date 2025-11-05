<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Record Payment</h1>

        <form action="{{ route('payments.store') }}" method="POST" class="max-w-xl bg-white shadow p-6 rounded">
            @csrf

            <div class="mb-3">
                <label class="block font-medium mb-1">Student</label>
                <select name="student_id" class="w-full border rounded p-2" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Fee</label>
                <select name="fee_id" class="w-full border rounded p-2" required>
                    <option value="">Select Fee</option>
                    @foreach($fees as $fee)
                        <option value="{{ $fee->id }}">{{ $fee->name }} ({{ $fee->schoolClass->name ?? 'N/A' }}) - ₦{{ number_format($fee->amount, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Amount Paid</label>
                <input type="number" name="amount_paid" step="0.01" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Payment Method</label>
                <input type="text" name="method" class="w-full border rounded p-2" placeholder="Cash / Transfer / POS">
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Notes</label>
                <textarea name="notes" class="w-full border rounded p-2"></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Payment</button>
        </form>
    </div>
</x-app-layout>
