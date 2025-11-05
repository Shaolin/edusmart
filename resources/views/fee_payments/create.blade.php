<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Record a Payments
             </h2>

           
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

            <form action="{{ route('fee_payments.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="p-4 bg-red-100 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc ml-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Select Class --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Select Class</label>
                    <select id="class-select"
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                        <option value="">-- Choose Class --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Student --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Select Student</label>
                    <select name="student_id" id="student-select" required
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                        <option value="">-- Choose Student --</option>
                    </select>
                </div>

                {{-- Select Fee --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Select Fee</label>
                    <select name="fee_id" required
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                        <option value="">-- Choose Fee --</option>
                        @foreach ($fees as $fee)
                            <option value="{{ $fee->id }}">
                                {{ $fee->schoolClass->name ?? 'No Class' }} - {{ $fee->name }} (₦{{ number_format($fee->amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount Paid --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Amount Paid (₦)</label>
                    <input type="number" name="amount" required min="1"
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                </div>

                {{-- Payment Date --}}
                <div>
                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Date of Payment</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                </div>

                {{-- Session & Term --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Session</label>
                        <input type="text" name="session" required placeholder="e.g. 2025/2026"
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">Term</label>
                        <select name="term" required
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-300">
                            <option value="First Term">First Term</option>
                            <option value="Second Term">Second Term</option>
                            <option value="Third Term">Third Term</option>
                        </select>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Save Payment
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- AJAX Script --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#class-select').change(function () {
            let classId = $(this).val();
            $('#student-select').html('<option>Loading...</option>');

            if (!classId) {
                $('#student-select').html('<option value="">-- Choose Student --</option>');
                return;
            }

            $.get('/students/by-class/' + classId, function (data) {
                $('#student-select').empty().append('<option value="">-- Choose Student --</option>');
                data.forEach(student => {
                    $('#student-select').append(`<option value="${student.id}">${student.name}</option>`);
                });
            }).fail(() => {
                alert('Error loading students. Please try again.');
            });
        });
    </script>

</x-app-layout>
