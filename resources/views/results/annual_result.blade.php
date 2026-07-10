<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                    Annual Result —
                    {{ $student->name ?? ($student->first_name . ' ' . $student->last_name ?? '—') }}
                </h2>

                <p class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    Academic Session:
                    {{ $session->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-6xl mx-auto px-3 sm:px-6 lg:px-8">

        <div id="annual-result-sheet"
            class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-4 sm:p-6 relative">

            {{-- Watermark --}}
            @if($school && $school->logo)
                <img
                    src="{{ asset('school_logos/' . $school->logo) }}"
                    class="absolute top-1/2 left-1/2 w-48 sm:w-72 opacity-5 -translate-x-1/2 -translate-y-1/2 rotate-12 z-0"
                    alt="School Logo">
            @endif

            {{-- Header --}}
            <div class="flex justify-between items-center relative z-10 mb-6">

                <div class="flex items-center gap-3">

                    @if($school && $school->logo)
                        <img
                            src="{{ asset('school_logos/' . $school->logo) }}"
                            class="h-16 w-16 object-contain rounded shadow"
                            alt="School Logo">
                    @endif

                    <div>

                        <h1 class="font-bold text-blue-700 text-xl">
                            {{ $school->name }}
                        </h1>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $school->address }}
                        </p>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $school->phone }}
                        </p>

                    </div>

                </div>

                <div>
                    <span
                        class="px-3 py-1 bg-indigo-600 text-white rounded font-semibold">
                        Annual Result
                    </span>
                </div>

            </div>

            {{-- Student Information --}}
            <div
                class="relative z-10 border-b border-gray-300 dark:border-gray-700 pb-4 mb-6">

                <h2 class="font-semibold mb-3">
                    Student Information
                </h2>

                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">

                    <p>
                        <strong>Name:</strong>
                        {{ $student->name }}
                    </p>

                    <p>
                        <strong>Admission No:</strong>
                        {{ $student->admission_number }}
                    </p>

                    <p>
                        <strong>Class:</strong>
                        {{ $student->schoolClass->name }}
                    </p>

                    <p>
                        <strong>Session:</strong>
                        {{ $session->name }}
                    </p>

                </div>

            </div>

            
           {{-- Annual Result Table --}}
<div class="mt-6 relative z-10">

    <h3 class="font-bold text-lg mb-4 text-blue-700 dark:text-blue-400">
        Annual Result
    </h3>

    <div class="overflow-x-auto">

        {{-- <table class="min-w-full border border-gray-300 dark:border-gray-700 text-sm"> --}}
            <table class="min-w-[900px] border border-gray-300 dark:border-gray-700 text-sm">

            <thead class="bg-gray-100 dark:bg-gray-800">

                <tr>

                    <th class="border px-3 py-2 text-left">Subject</th>

                    <th class="border px-3 py-2 text-center">1st</th>

                    <th class="border px-3 py-2 text-center">2nd</th>

                    <th class="border px-3 py-2 text-center">3rd</th>

                    <th class="border px-3 py-2 text-center">Total</th>

                    <th class="border px-3 py-2 text-center">Average</th>
                    <th class="border px-3 py-2 text-center">Grade</th>
                    <th class="border px-3 py-2 text-center">Remark</th>
                </tr>

            </thead>

            <tbody>

                @forelse($cumulativeResults as $result)

                    <tr>

                        <td class="border px-3 py-2 font-medium">
                            {{ $result->subject->name }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ $result->first }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ $result->second }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ $result->third }}
                        </td>

                        <td class="border px-3 py-2 text-center font-semibold">
                            {{ $result->total }}
                        </td>

                        <td class="border px-3 py-2 text-center font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($result->average, 2) }}
                        </td>
                        <td class="border px-3 py-2 text-center font-bold">
    {{ $result->grade }}
</td>

<td class="border px-3 py-2">
    {{ $result->remark }}
</td>

                    </tr>

                @empty

                    <tr>

                        
                            <td colspan="8" class="border px-3 py-4 text-center text-gray-500">
                            No annual results found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- Annual Summary --}}
<div class="mt-6 border rounded-lg border-gray-300 dark:border-gray-700 p-4">

    <h3 class="font-bold text-blue-700 dark:text-blue-400 mb-4 uppercase">
        Annual Summary
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Annual Total Score
            </p>

            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ number_format($annualTotal, 2) }}
            </p>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Annual Average
            </p>

            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($annualAverage, 2) }}%
            </p>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Annual Position
            </p>

            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ $annualPosition ?? '—' }}
            </p>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Students in Class
            </p>

            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                {{ $totalStudents }}
            </p>
        </div>

    </div>

</div>
            {{-- Print --}}
            <div class="mt-8 text-center no-print">

                <button
                    onclick="window.print()"
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded">

                    🖨️ Print Annual Result

                </button>

            </div>

        </div>

    </div>

</x-app-layout>