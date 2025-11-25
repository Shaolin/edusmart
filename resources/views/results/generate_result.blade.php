


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                    Result — {{ $student->name ?? ($student->first_name . ' ' . $student->last_name ?? '—') }}
                </h2>
                <p class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ $term->name ?? '—' }} | {{ $session->name ?? '—' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
        <div id="result-sheet" class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-4 sm:p-6 relative font-sans">
            
            {{-- Watermark --}}
            @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}"
                     class="absolute top-1/2 left-1/2 w-48 sm:w-72 opacity-5 -translate-x-1/2 -translate-y-1/2 rotate-12 z-0">
            @endif
            

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 relative z-10 mb-6">
                <div class="flex items-center gap-3">
                    @if($school && $school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                    @endif

                    <div class="text-sm sm:text-base text-gray-800 dark:text-gray-100">
                        <h1 class="font-bold text-blue-800 dark:text-blue-400 text-lg sm:text-xl">
                            {{ $school->name ?? 'School Name' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ $school->address ?? '—' }}</p>
                        <p class="text-gray-600 dark:text-gray-300">
                            Contact: <span class="text-blue-600 dark:text-blue-400">{{ $school->phone ?? $school->contact ?? '—' }}</span>
                        </p>
                        @if(!empty($school->email))
                            <p class="truncate text-gray-600 dark:text-gray-300">Email: <span class="text-purple-600 dark:text-purple-400">{{ $school->email }}</span></p>
                        @endif
                        @if(!empty($school->website))
                            <p class="truncate text-gray-600 dark:text-gray-300">Website: <span class="text-purple-600 dark:text-purple-400">{{ $school->website }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <span class="px-2 py-1 bg-yellow-200 dark:bg-yellow-700 rounded font-semibold text-xs sm:text-sm text-yellow-800 dark:text-yellow-100">
                        Result Sheet
                    </span>
                </div>
            </div>

            {{-- Student Info --}}
            <div class="relative z-10 border-b border-gray-300 dark:border-gray-700 pb-4 mb-6 text-gray-800 dark:text-gray-100">
                <h2 class="font-semibold mb-2 text-sm sm:text-base">Student Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                    <p><strong>Name:</strong> <span class="text-blue-600 dark:text-blue-400">{{ $student->name ?? '—' }}</span></p>
                    <p><strong>Admission No:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $student->admission_number ?? '—' }}</span></p>
                    <p><strong>Class:</strong> <span class="text-green-600 dark:text-green-400">{{ $student->schoolClass->name ?? '—' }}</span></p>
                    <p><strong>Term:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $term->name ?? '—' }}</span></p>
                    <p><strong>Session:</strong> <span class="text-purple-600 dark:text-purple-400">{{ $session->name ?? '—' }}</span></p>
                    <p><strong>Date:</strong> <span class="text-gray-600 dark:text-gray-300">{{ now()->format('d M, Y') }}</span></p>
                </div>
            </div>

            {{-- Results Table --}}
            <div class="overflow-x-auto relative z-10 rounded-lg">
                <table class="min-w-full border border-gray-300 dark:border-gray-700 text-xs sm:text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
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
                        @php $totalSum = 0; $count = 0; @endphp
                        @foreach($results as $result)
                            @php $totalSum += $result->total_score ?? 0; $count++; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-indigo-600 dark:text-indigo-400">{{ $result->subject->name ?? '—' }}</td>
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->test_score ?? 0 }}</td>
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->exam_score ?? 0 }}</td>
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->total_score ?? 0 }}</td>
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center font-semibold">{{ $result->grade ?? '—' }}</td>
                                <td class="border px-2 py-1 sm:px-4 sm:py-2 text-center">{{ $result->remark ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            @if($count > 0)
            <div class="mt-5 text-xs sm:text-sm space-y-1 text-gray-800 dark:text-gray-100">
                <p><strong>Total Score:</strong> {{ $totalSum }}</p>
                <p><strong>Average:</strong> {{ $count > 0 ? number_format($totalSum / $count, 2) : 0 }}</p>
                <p><strong>Position:</strong> {{ $position ?? '—' }} out of {{ $total_students ?? '—' }} students</p>
            </div>
            @endif

            {{-- Teacher Remark --}}
            @if($results->first()?->teacher_remark)
                <div class="mt-5 text-xs sm:text-sm text-gray-800 dark:text-gray-100">
                    <h2 class="font-semibold mb-1">Teacher's Remark:</h2>
                    <p class="italic">{{ $results->first()->teacher_remark }}</p>
                </div>
            @endif

            {{-- send result-sheet whatsapp --}}

            @php
    $parentPhone = preg_replace('/^0/', '234', $student->guardian_phone ?? $student->guardian->phone);

    // Generate PDF and get file path (call the controller method)
    $pdfPath = asset('storage/results/' . $student->id . '.pdf'); // ensure PDF exists

    $message = "Hello, your child's result is ready. Download PDF here: $pdfPath";
    $encodedMessage = urlencode($message);
@endphp

<div class="mt-4 text-center no-print">
    <a href="{{ route('students.sendWhatsapp', $student->id) }}"
       target="_blank"
       class="px-4 py-2 bg-blue-600 text-white rounded text-sm sm:text-base">
       📄 Send Result to Parent (WhatsApp PDF)
    </a>
</div>




            {{-- Edit Button --}}
            @if($results->count() > 0)
            <div class="mt-4 text-center no-print">
                <a href="{{ route('results.editAll', ['student_id'=>$student->id,'term_id'=>$term->id ?? 0,'session_id'=>$session->id ?? 0]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm sm:text-base">
                    ✏️ Edit Results
                </a>
            </div>
            @endif

            {{-- Print Button --}}
            <div class="mt-6 text-center no-print">
                <button onclick="window.print()"
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded text-sm sm:text-base">
                    🖨️ Print / Save as PDF
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
