<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold">
            Enter Results — {{ $student->name }} ({{ $student->schoolClass->name }})
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 sm:p-6 text-gray-900 dark:text-gray-100">

        {{-- ✅ Flash Messages --}}
        @if (session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-100 text-green-800 border border-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-3 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-3 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="resultForm" action="{{ route('results.storeResult') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            {{-- 🔹 Session and Term --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-1">Session</label>
                    <select name="session_id"
                            class="w-full border-gray-300 rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Term</label>
                    <select name="term_id"
                            class="w-full border-gray-300 rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 🔹 Subjects Table (Mobile Scrollable) --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm mb-4 text-sm">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left whitespace-nowrap">Subject</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Test (40)</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Exam (60)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $index => $subject)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $subject->name }}
                                    <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="test_score[]"
                                           max="40"
                                           class="score-input w-24 sm:w-32 border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('test_score.' . $index) }}">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="exam_score[]"
                                           max="60"
                                           class="score-input w-24 sm:w-32 border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('exam_score.' . $index) }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Teacher's General Remark --}}
            <div class="mt-4">
                <label for="teacher_remark" class="block font-semibold mb-1">
                    Teacher's Remark
                </label>
                <textarea name="teacher_remark" id="teacher_remark" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-100"
                          placeholder="Enter a general remark...">{{ old('teacher_remark') }}</textarea>
            </div>

            {{-- 🔹 Submit Button --}}
            <button type="submit"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full sm:w-auto">
                Save Results
            </button>
        </form>
    </div>

    {{-- ⚡ Confirmation + Highlight Script --}}
    <script>
        // Existing script stays the same — no changes needed for responsiveness
    </script>

</x-app-layout>
