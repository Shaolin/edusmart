<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-bold">Class Ranking: {{ $class->name }}</h2>
    </x-slot>

    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow">

        {{-- Responsive Table Wrapper --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-sm sm:text-base">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <th class="px-3 sm:px-4 py-2 text-left">#</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Name</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Total Score</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Average</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Position</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-3 sm:px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-3 sm:px-4 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $student['name'] }}</td>
                            <td class="px-3 sm:px-4 py-2">{{ $student['total_score'] }}</td>
                            <td class="px-3 sm:px-4 py-2">{{ number_format($student['average'], 2) }}</td>
                            <td class="px-3 sm:px-4 py-2">{{ $student['position'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Extra Small Screen Card View --}}
    <style>
        @media (max-width: 480px) {
            table thead {
                display: none;
            }

            table, table tbody, table tr, table td {
                display: block;
                width: 100%;
            }

            table tr {
                margin-bottom: 12px;
                background: rgba(0,0,0,0.03);
                border-radius: 8px;
                padding: 8px 10px;
            }

            table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: none !important;
            }

            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: 600;
                color: #555;
                text-align: left;
            }
        }
    </style>
</x-app-layout>
