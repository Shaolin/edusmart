<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Edit Fee Payment
             </h2>

        
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="p-3 mb-4 bg-green-100 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-3 mb-4 bg-red-100 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('fee_payments.update', $payment->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Student --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Student</label>
                        <input type="text"
                               value="{{ $payment->student->first_name ?? $payment->student->name }} {{ $payment->student->last_name ?? '' }}"
                               readonly
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200" />
                    </div>

                    {{-- Class --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Class</label>
                        <input type="text"
                               value="{{ $payment->student->schoolClass->name ?? '—' }}"
                               readonly
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200" />
                    </div>

                    {{-- Fee Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fee Type</label>
                        <input type="text"
                               value="{{ $payment->fee->name ?? '—' }}"
                               readonly
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200" />
                    </div>

                    {{-- Amount Paid --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Amount Paid (₦)</label>
                        <input type="number" name="amount"
                               value="{{ old('amount', $payment->amount) }}"
                               step="0.01"
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-200 focus:ring focus:ring-blue-300" />
                    </div>

                    {{-- Session --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Session</label>
                        <input type="text" name="session"
                               value="{{ old('session', $payment->session) }}"
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-200 focus:ring focus:ring-blue-300" />
                    </div>

                    {{-- Term --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Term</label>
                        <select name="term"
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-200 focus:ring focus:ring-blue-300">
                            <option value="first" {{ $payment->term == 'first' ? 'selected' : '' }}>First Term</option>
                            <option value="second" {{ $payment->term == 'second' ? 'selected' : '' }}>Second Term</option>
                            <option value="third" {{ $payment->term == 'third' ? 'selected' : '' }}>Third Term</option>
                        </select>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-3">
                    <a href="{{ route('fee_payments.index') }}"
                        class="w-full sm:w-auto bg-gray-500 text-white px-4 py-2 rounded-lg text-center hover:bg-gray-600 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Payment
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
