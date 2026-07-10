<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold">
            School Settings
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 sm:p-8 border border-gray-200 dark:border-gray-700">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-300 bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-300 bg-red-100 p-4 text-red-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('school-settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Next Term Begins --}}
                <div>
                    <label for="next_term_begins" class="block font-semibold mb-2">
                        Next Term Begins
                    </label>

                    <input
                        type="date"
                        id="next_term_begins"
                        name="next_term_begins"
                        value="{{ old('next_term_begins', optional($setting->next_term_begins)->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Next Term School Fees --}}
                <div>
                    <label for="next_term_school_fees" class="block font-semibold mb-2">
                        Next Term School Fees (₦)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        id="next_term_school_fees"
                        name="next_term_school_fees"
                        value="{{ old('next_term_school_fees', $setting->next_term_school_fees) }}"
                        placeholder="e.g. 35000"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            <div class="mt-8">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow">
                    Save Settings
                </button>
            </div>

        </form>

    </div>
</x-app-layout>