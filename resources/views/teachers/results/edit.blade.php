<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            ✏️ Edit Results — {{ $student->name }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6 p-6">

        {{-- 🔹 FILTER FORM (GET) --}}
        <form method="GET" class="flex flex-wrap gap-4 mb-6">

            <input type="hidden" name="student" value="{{ $student->id }}">

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Session</label>
                <select name="session_id"
                        onchange="this.form.submit()"
                        class="border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}"
                            {{ $session->id == $sessionId ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Term</label>
                <select name="term_id"
                        onchange="this.form.submit()"
                        class="border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}"
                            {{ $term->id == $termId ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- 🔹 UPDATE FORM (POST) --}}
        <form method="POST" action="{{ route('teachers.results.update', $student->id) }}">
            @csrf
            @method('PUT')

            {{-- LOCK VALUES --}}
            <input type="hidden" name="session_id" value="{{ $sessionId }}">
            <input type="hidden" name="term_id" value="{{ $termId }}">

            {{-- TABLE --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <tr>
                            <th class="p-3 text-left">Subject</th>
                            <th class="p-3 text-left">Test</th>
                            <th class="p-3 text-left">Exam</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($subjects as $index => $subject)
                            @php
                                $result = $existingResults[$subject->id] ?? null;
                            @endphp

                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 text-gray-800 dark:text-gray-100">
                                    {{ $subject->name }}
                                    <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                                </td>

                                <td class="p-3">
                                    <input type="number" name="test_score[]"
                                        value="{{ old('test_score.' . $index, $result->test_score ?? '') }}"
                                        class="w-24 border rounded-md dark:bg-gray-700 dark:text-gray-100">
                                </td>

                                <td class="p-3">
                                    <input type="number" name="exam_score[]"
                                        value="{{ old('exam_score.' . $index, $result->exam_score ?? '') }}"
                                        class="w-24 border rounded-md dark:bg-gray-700 dark:text-gray-100">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- REMARK --}}
                <div class="mt-4">
                    <textarea name="teacher_remark"
                        class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-gray-100"
                        placeholder="Teacher remark...">{{ old('teacher_remark', $teacherRemark) }}</textarea>
                </div>

                <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>