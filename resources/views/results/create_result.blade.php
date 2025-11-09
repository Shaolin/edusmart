<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold">
            Enter Results — {{ $student->name }} ({{ $student->schoolClass->name }})
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 sm:p-8 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700">

        {{-- ✅ Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
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

            {{-- 🔹 Session & Term --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Session</label>
                    <select name="session_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                                   dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Term</label>
                    <select name="term_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                                   dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 📘 Subjects Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm text-sm border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Test (40)</th>
                            <th class="px-4 py-3 text-left">Exam (60)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $index => $subject)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-3 font-medium">
                                    {{ $subject->name }}
                                    <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="test_score[]"
                                           max="40"
                                           class="w-28 sm:w-36 border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-base
                                                  dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                                           value="{{ old('test_score.' . $index) }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="exam_score[]"
                                           max="60"
                                           class="w-28 sm:w-36 border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-base
                                                  dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                                           value="{{ old('exam_score.' . $index) }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 📝 Teacher's Remarks --}}
            <div class="mt-6">
                <label for="teacher_remark" class="block font-semibold mb-2">
                    Teacher's Remark
                </label>
                <textarea name="teacher_remark" id="teacher_remark" rows="4"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                                 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                          placeholder="Enter a general remark...">{{ old('teacher_remark') }}</textarea>
            </div>

            {{-- ✅ Submit Button --}}
            <button type="submit"
                    class="mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-base font-semibold shadow-md
                           w-full sm:w-auto transition-all">
                Save Results
            </button>
        </form>
    </div>
</x-app-layout>
