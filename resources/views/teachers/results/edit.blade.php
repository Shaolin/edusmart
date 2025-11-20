<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">
            Enter Results — {{ $student->name }} ({{ $student->schoolClass->name ?? '-' }})
        </h2>
    </x-slot>

    <div class="p-6">
        <form action="{{ route('teachers.results.update', $student->id) }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            {{-- Session & Term --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2">Session</label>
                    <select name="session_id" class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $sessionId ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Term</label>
                    <select name="term_id" class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100">
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ $term->id == $termId ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto mb-6">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Test (40)</th>
                            <th class="px-4 py-3 text-left">Exam (60)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $index => $subject)
                            @php
                                $result = $existingResults[$subject->id] ?? null;
                            @endphp
                            <tr class="{{ $index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                    {{ $subject->name }}
                                    <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="test_score[]" min="0" max="40"
                                           class="w-28 sm:w-36 border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('test_score.' . $index, $result->test_score ?? '') }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="exam_score[]" min="0" max="60"
                                           class="w-28 sm:w-36 border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                           value="{{ old('exam_score.' . $index, $result->exam_score ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4 mb-6">
                @foreach($subjects as $index => $subject)
                    @php
                        $result = $existingResults[$subject->id] ?? null;
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Subject:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $subject->name }}</span>
                            <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-500 dark:text-gray-400 text-sm">Test (40)</label>
                            <input type="number" name="test_score[]" min="0" max="40"
                                   class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                   value="{{ old('test_score.' . $index, $result->test_score ?? '') }}">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-500 dark:text-gray-400 text-sm">Exam (60)</label>
                            <input type="number" name="exam_score[]" min="0" max="60"
                                   class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100"
                                   value="{{ old('exam_score.' . $index, $result->exam_score ?? '') }}">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Teacher's Remark --}}
            <div class="mb-6">
                <label class="block font-semibold mb-2">Teacher's Remark</label>
                <textarea name="teacher_remark" rows="4"
                          class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:text-gray-100"
                          placeholder="Enter a general remark...">{{ old('teacher_remark', $teacherRemark) }}</textarea>
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg w-full sm:w-auto">
                Save Results
            </button>
        </form>
    </div>
</x-app-layout>
