<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold">
            ✏️ Edit Results — {{ $student->name }} ({{ $student->schoolClass->name }})
        </h2>
        <p class="text-gray-500 mt-1">
            Term: {{ $term->name }} | Session: {{ $session->name }}
        </p>
    </x-slot>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">

        {{-- ✅ Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-3 rounded-lg bg-yellow-100 text-yellow-900 border border-yellow-300">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Update route instead of storeResult --}}
        <form id="editResultForm" 
              action="{{ route('results.update', $student->id) }}" 
              method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="term_id" value="{{ $term->id }}">
            <input type="hidden" name="session_id" value="{{ $session->id }}">

            {{-- 🔹 Subjects Table --}}
            <table class="min-w-full bg-gray-50 rounded-lg shadow-sm mb-4">
                <thead>
                    <tr class="bg-gray-200 text-gray-800 dark:text-gray-100">
                        <th class="px-4 py-2 text-left">Subject</th>
                        <th class="px-4 py-2 text-left">Test (40)</th>
                        <th class="px-4 py-2 text-left">Exam (60)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $index => $subject)
                        @php
                            // Find existing result for this subject
                            $existing = $results->firstWhere('subject_id', $subject->id);
                            $test = $existing->test_score ?? '';
                            $exam = $existing->exam_score ?? '';
                            $missing = ($test === '' || $exam === '');
                        @endphp
                        <tr class="{{ $missing ? 'bg-yellow-50 dark:bg-yellow-900/30' : '' }}">
                            <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">
                                {{ $subject->name }}
                                <input type="hidden" name="subject_id[]" value="{{ $subject->id }}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number"
                                       name="test_score[]"
                                       max="40"
                                       class="score-input w-24 border-gray-300 rounded-lg {{ $missing ? 'border-yellow-400' : '' }}"
                                       value="{{ old('test_score.' . $index, $test) }}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number"
                                       name="exam_score[]"
                                       max="60"
                                       class="score-input w-24 border-gray-300 rounded-lg {{ $missing ? 'border-yellow-400' : '' }}"
                                       value="{{ old('exam_score.' . $index, $exam) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Teacher's General Remark --}}
<div class="mt-4">
    <label for="teacher_remark" class="block font-semibold text-gray-700 dark:text-gray-200 mb-1">Teacher's Remark</label>
    <textarea name="teacher_remark" id="teacher_remark" rows="3"
        class="w-full border border-gray-300 dark:border-gray-700 rounded p-2 focus:outline-none focus:ring focus:ring-blue-300 dark:bg-gray-900 dark:text-gray-100"
        placeholder="Enter a general remark about the student's performance or behaviour">{{ old('teacher_remark', $results->first()->teacher_remark ?? '') }}</textarea>
</div>


            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                💾 Update Results
            </button>
        </form>
    </div>

    {{-- ⚡ Confirmation + Highlight Script --}}
    <script>
        const form = document.getElementById('editResultForm');
        const testInputs = document.querySelectorAll('input[name^="test_score"]');
        const examInputs = document.querySelectorAll('input[name^="exam_score"]');
        const subjectLabels = document.querySelectorAll('tbody td:first-child');
        let allowSubmit = false;

        const topAlert = document.createElement('div');
        topAlert.id = 'topAlert';
        topAlert.className =
            'fixed top-0 left-0 right-0 z-50 bg-yellow-100 text-yellow-900 border-b border-yellow-400 shadow-md p-4 hidden transition-all duration-300';
        topAlert.innerHTML = `
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <span id="alertMessage" class="flex-1"></span>
                <div class="flex gap-2 mt-2 md:mt-0">
                    <button id="proceedSave"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                        ✅ Yes, Save Anyway
                    </button>
                    <button id="cancelAlert"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1 rounded-lg text-sm">
                        ❌ Cancel
                    </button>
                </div>
            </div>
        `;
        document.body.prepend(topAlert);

        const proceedBtn = document.getElementById('proceedSave');
        const cancelBtn = document.getElementById('cancelAlert');

        cancelBtn.addEventListener('click', () => {
            topAlert.classList.add('hidden');
            allowSubmit = false;
        });

        proceedBtn.addEventListener('click', () => {
            allowSubmit = true;
            topAlert.classList.add('hidden');
            form.submit();
        });

        form.addEventListener('submit', function (e) {
            if (allowSubmit) return;
            e.preventDefault();

            let missing = [];
            testInputs.forEach((test, i) => {
                const exam = examInputs[i];
                const subjectName = subjectLabels[i].innerText.trim();
                if (!test.value || !exam.value) missing.push(subjectName);
            });

            if (missing.length > 0) {
                document.getElementById('alertMessage').innerHTML = `
                    ⚠️ Some subjects are still missing scores:<br>
                    <strong>${missing.join(', ')}</strong><br><br>
                    Do you still want to save the filled ones?
                `;
                topAlert.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                allowSubmit = true;
                form.submit();
            }
        });
    </script>
</x-app-layout>
