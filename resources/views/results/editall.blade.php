<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                ✏️ Edit Results
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $student->name }} • {{ $student->schoolClass->name }}  
                <span class="mx-2">|</span>
                {{ $term->name }} • {{ $session->name }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6 p-6">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/40 dark:text-green-200 dark:border-green-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">

            <form id="editResultForm" method="POST" action="{{ route('results.update', $student->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="term_id" value="{{ $term->id }}">
                <input type="hidden" name="session_id" value="{{ $session->id }}">

                {{-- Table --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Subject</th>
                                <th class="px-4 py-3 text-left font-semibold">Test</th>
                                <th class="px-4 py-3 text-left font-semibold">Exam</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($subjects as $index => $subject)
                                @php
                                    $existing = $results->firstWhere('subject_id', $subject->id);
                                    $test = $existing->test_score ?? '';
                                    $exam = $existing->exam_score ?? '';
                                    $missing = ($test === '' || $exam === '');
                                @endphp

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                        {{ $subject->name }}
                                        <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="number"
                                            name="test_score[]"
                                            max="40"
                                            value="{{ old('test_score.' . $index, $test) }}"
                                            class="w-24 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100 
                                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                                            {{ $missing ? 'border-yellow-400' : '' }}">
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="number"
                                            name="exam_score[]"
                                            max="60"
                                            value="{{ old('exam_score.' . $index, $exam) }}"
                                            class="w-24 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100 
                                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                                            {{ $missing ? 'border-yellow-400' : '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Remark --}}
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Teacher's Remark
                    </label>
                    <textarea name="teacher_remark" rows="3"
                        class="w-full px-4 py-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100
                        focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="Write a short performance summary...">{{ old('teacher_remark', $results->first()->teacher_remark ?? '') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex justify-between items-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tip: Fill all scores to avoid warnings
                    </p>

                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition">
                        💾 Update Results
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>