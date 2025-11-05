<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">
            {{ $student->name }} — {{ $term->name }} ({{ $session->name }})
        </h2>
       
    </x-slot>

    <div class="max-w-5xl mx-auto mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        {{-- Success & Warning Messages --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Student Info --}}
        <div class="mb-6 text-gray-700 dark:text-gray-300">
            <p><strong>Student:</strong> {{ $student->name }}</p>
            <p><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</p>
            <p><strong>Term:</strong> {{ $term->name }}</p>
            <p><strong>Session:</strong> {{ $session->name }}</p>
        </div>

        {{-- Results Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-center">Test (40)</th>
                        <th class="p-3 text-center">Exam (60)</th>
                        <th class="p-3 text-center">Total</th>
                        <th class="p-3 text-center">Grade</th>
                        <th class="p-3 text-center">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSum = 0; $count = 0; @endphp
                    @forelse ($results as $result)
                        <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="p-3">{{ $result->subject->name }}</td>
                            <td class="p-3 text-center">{{ $result->test_score }}</td>
                            <td class="p-3 text-center">{{ $result->exam_score }}</td>
                            <td class="p-3 text-center font-semibold">{{ $result->total_score }}</td>
                            <td class="p-3 text-center font-semibold">{{ $result->grade }}</td>
                            <td class="p-3 text-center">{{ $result->remark }}</td>
                        </tr>
                        @php 
                            $totalSum += $result->total_score;
                            $count++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-600 dark:text-gray-300">
                                No results recorded for this term and session.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Average --}}
        @if ($count > 0)
            <div class="mt-4 text-right text-gray-800 dark:text-gray-100">
                <strong>Average Score:</strong> {{ number_format($totalSum / $count, 2) }}
            </div>
        @endif

        {{-- Position --}}
        @if(isset($position))
            <div class="mt-4 text-gray-800 dark:text-gray-100">
                <p><strong>Average Score:</strong> {{ number_format($average, 2) }}</p>
                <p><strong>Position:</strong> {{ $position }}{{ getOrdinalSuffix($position) }} out of {{ $total_students }}</p>
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-6 flex justify-between items-center flex-wrap gap-2">
            <a href="{{ route('results.showStudents', ['class_id' => $student->schoolClass->id]) }}" 
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                ← Back to Students
            </a>
             
            <div class="flex gap-2">
                <a href="{{ route('results.generate', ['student_id' => $student->id, 'term_id' => $term->id, 'session_id' => $session->id]) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    📄 Generate Result
                </a>
                 
                @if($results->isNotEmpty())
                    <a href="{{ route('results.editAll', [
                        'student_id' => $student->id,
                        'term_id' => $term->id,
                        'session_id' => $session->id
                    ]) }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        ✏️ Edit Scores
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
