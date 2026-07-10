<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                    Results — {{ $student->name }}
                </h2>
                <p class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ $term->name }} | {{ $session->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto relative" id="result-sheet">
        {{-- Watermark --}}
        @if($school && $school->logo)
           
                 <img src="{{ asset('school_logos/' . $school->logo) }}"
                 class="absolute top-1/2 left-1/2 w-48 sm:w-72 opacity-5 -translate-x-1/2 -translate-y-1/2 rotate-12 z-0"
                 alt="School Logo">
        @endif

        {{-- School Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 relative z-10">
            <div class="flex items-center gap-3">
                @if($school && $school->logo)
                 
                    <img src="{{ asset('school_logos/' . $school->logo) }}"
                    class="h-16 w-16 object-contain rounded shadow"
                    alt="School Logo">
                @endif
                <div class="text-sm sm:text-base text-gray-800 dark:text-gray-100">
                    <h1 class="font-bold text-blue-800 dark:text-blue-400 text-lg sm:text-xl">{{ $school->name ?? 'School Name' }}</h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ $school->address ?? '' }}</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Contact: <span class="text-blue-600 dark:text-blue-400">{{ $school->phone ?? 'Not set' }}</span>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-700 rounded font-semibold text-xs sm:text-sm text-yellow-800 dark:text-yellow-100">
                    Result Sheet
                </span>
            </div>
        </div>

        {{-- Student Info Card --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 relative z-10 text-gray-800 dark:text-gray-100">
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Student Info</h3>
                <p><strong>Name:</strong> {{ $student->name }}</p>
                <p><strong>Admission No:</strong> {{ $student->admission_number ?? '—' }}</p>
                <p><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</p>
            </div>
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Session & Term</h3>
                <p><strong>Term:</strong> {{ $term->name }}</p>
                <p><strong>Session:</strong> {{ $session->name }}</p>
                <p><strong>Date:</strong> {{ now()->format('d M, Y') }}</p>
            </div>
            <div class="p-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold text-sm sm:text-base mb-1">Summary</h3>
                @php
                    $totalScore = $results->sum('total_score');
                    $subjectCount = $results->count();
                    $average = $subjectCount ? number_format($totalScore / $subjectCount, 2) : '0.00';
                @endphp
                <p><strong>Total Score:</strong> {{ $totalScore }}</p>
                <p><strong>Average:</strong> {{ $average }}</p>
                <p><strong>Position:</strong> {{ $position ?? '—' }} out of {{ $total_students ?? '—' }}</p>
                <div class="text-sm text-gray-700 dark:text-gray-200">
                   
                </div>
            </div>
        </div>

       {{-- Attendance Summary --}}
<div class="mb-6 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 p-4 relative z-10">

    <h3 class="font-bold text-blue-700 dark:text-blue-400 uppercase border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
        Attendance Summary
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-800 dark:text-gray-100">

        {{-- Time School Opened --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-center shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                Time School Opened
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ $attendanceSummary->school_opened ?? '—' }}
            </p>
        </div>

        {{-- Times Present --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-center shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                Times Present
            </p>

            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">
                {{ $attendanceSummary->times_present ?? '—' }}
            </p>
        </div>

        {{-- Times Absent --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-center shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                Times Absent
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
                {{ $attendanceSummary->times_absent ?? '—' }}
            </p>
        </div>

    </div>

</div>

{{-- Results Table --}}

        {{-- Results Table --}}
        <div class="overflow-x-auto relative z-10 rounded-lg mb-6">
            <table class="min-w-full border border-gray-300 dark:border-gray-700 text-xs sm:text-sm">
                <thead class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100">
                    <tr>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-left">Subject</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Test (40)</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Exam (60)</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Total</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Grade</th>
                        <th class="border px-2 py-1 sm:px-4 sm:py-2 text-center">Remark</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 dark:text-gray-100">
                    @foreach($results as $result)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-indigo-600 dark:text-indigo-400">{{ $result->subject->name }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->test_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->exam_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->total_score }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->grade }}</td>
                            <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

           {{-- Next Term School Fees --}}
<div class="mt-6 border rounded-lg border-gray-300 dark:border-gray-700 p-4">

    <h3 class="font-bold text-blue-700 dark:text-blue-400 mb-3 uppercase">
        Next Term School Fees
    </h3>

    @if($nextTermFee)

        <div class="space-y-2 text-sm text-gray-800 dark:text-gray-100">

            <p>
                <strong>Term:</strong>

                @switch($nextTermFee->term)
                    @case('first')
                        First Term
                        @break

                    @case('second')
                        Second Term
                        @break

                    @case('third')
                        Third Term
                        @break

                    @default
                        {{ ucfirst($nextTermFee->term) }}
                @endswitch
            </p>

            <p>
                <strong>Session:</strong>
                {{ $nextTermFee->session }}
            </p>

            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                ₦{{ number_format($nextTermFee->amount, 2) }}
            </p>

        </div>

    @else

        <p class="text-red-600 dark:text-red-400">
            Next term school fees have not been published yet.
        </p>

    @endif

</div>

        {{-- Teacher Remark --}}
        @if($results->first() && $results->first()->teacher_remark)
            <div class="mt-5 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                <strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}
            </div>
        @endif

        {{-- Next term begins --}}

        @if($setting && $setting->next_term_begins)
    <div class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4">
        <p class="font-bold text-blue-700 dark:text-blue-400 uppercase">
            NEXT TERM BEGINS
        </p>

        <p class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $setting->next_term_begins->format('d F, Y') }}
        </p>
    </div>
@endif

        {{-- Action Buttons --}}
       
        <div class="mt-6 flex flex-col sm:flex-row sm:flex-wrap gap-3 no-print">

            {{-- <a href="{{ route('teachers.results.edit', $student->id) }}?session={{ request('session') }}&term={{ request('term') }}"
               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-center flex-1 sm:flex-none">
                ✏️ Edit Results
            </a> --}}

            <a href="{{ route('teachers.results.edit', [
    'student' => $student->id,
    'session_id' => request('session_id'),
    'term_id' => request('term_id')
]) }}"
class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-center flex-1 sm:flex-none">
    ✏️ Edit Results
</a>
        
            <a href="{{ route('teachers.results.download', [$student->id, 'session' => request('session'), 'term' => request('term')]) }}"
               class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded text-center flex-1 sm:flex-none">
                📥 Download PDF
            </a>
        
            <a href=" {{ route('students.sendWhatsapp', $student->id) }}"
               target="_blank"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-center flex-1 sm:flex-none">
                📄 Send Result
            </a>
        
        </div>
        
    </div>

    <style>
        @media print {
            body * { visibility: hidden !important; }
            #result-sheet, #result-sheet * { visibility: visible !important; }
            #result-sheet { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
</x-app-layout>
