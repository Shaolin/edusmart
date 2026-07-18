<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    📊 Class Broadsheet
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    {{ $class->name }}
                </p>
            </div>

            <a href="{{ url()->previous() }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4">

            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">

                <div class="overflow-x-auto">

                    <table class="min-w-full border border-gray-300 dark:border-gray-700">

                        <thead>

                            <tr class="bg-gray-100 dark:bg-gray-800">

                                <th class="border px-4 py-3 sticky left-0 bg-gray-100 dark:bg-gray-800 z-10">
                                    Student
                                </th>

                                @foreach($subjects as $subject)
                                    <th class="border px-4 py-3 whitespace-nowrap text-center">
                                        {{ $subject->name }}
                                    </th>
                                @endforeach

                                <th class="border px-4 py-3 bg-blue-100 dark:bg-blue-900">
                                    Average
                                </th>

                                <th class="border px-4 py-3 bg-green-100 dark:bg-green-900">
                                    Position
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        @foreach($class->students as $student)

                            @php
                                $sum = 0;
                                $count = 0;
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">

                                <td class="border px-4 py-3 font-semibold sticky left-0 bg-white dark:bg-gray-900">
                                    {{ $student->name }}
                                </td>

                                @foreach($subjects as $subject)

                                    @php
                                        $key = $student->id . '_' . $subject->id;

                                        $result = $results[$key] ?? null;

                                        $score = $result?->total_score;

                                        if(!is_null($score)){
                                            $sum += $score;
                                            $count++;
                                        }
                                    @endphp

                                    <td class="border px-4 py-3 text-center">

                                        {{ $score ?? '-' }}

                                    </td>

                                @endforeach

                                <td class="border px-4 py-3 font-bold text-center bg-blue-50 dark:bg-blue-950">

                                    {{ $count ? number_format($sum / $count,2) : '-' }}

                                </td>

                               <td class="border px-4 py-3 text-center bg-green-50 dark:bg-green-950 font-bold">
    {{ $classPositions[$student->id] ?? '-' }}
</td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <a href="{{ route('results.broadsheet.download', [
    'class_id' => $class->id,
    'term_id' => request('term_id'),
    'session_id' => request('session_id'),
]) }}"
class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md shadow">
    📄 Download Broadsheet PDF
</a>

    </div>

</x-app-layout>