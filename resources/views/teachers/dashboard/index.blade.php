<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">

            <!-- Dashboard Title -->
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 
                       bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 
                       transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                Teacher Dashboard
            </h2>

            <!-- Welcome Message -->
            <h2 class="font-semibold text-lg sm:text-xl rounded-lg px-4 py-2 
                       bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 
                       transition-colors duration-300 w-full sm:w-auto text-center sm:text-left truncate">
                Welcome back, {{ auth()->user()->name }}
            </h2>
        </div>
    </x-slot>

    <!-- Dashboard Cards -->
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            @php
                $cards = [
                    ['name' => 'Students',   'route' => 'teachers.students', 'count' => $totalStudents ?? 0, 'color' => 'blue'],
                    ['name' => 'Results',    'route' => 'teachers.results',  'count' => 'Manage',             'color' => 'red'],
                    ['name' => 'My Classes', 'route' => 'teachers.classes',  'count' => $classes->count() ?? 0, 'color' => 'purple'],
                    ['name' => 'Guardians',  'route' => '#',                 'count' => $guardians->count() ?? 0, 'color' => 'green'],
                ];
            @endphp

            @foreach($cards as $card)
                <a href="{{ $card['route'] !== '#' ? route($card['route']) : '#' }}"
                   class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition 
                          flex flex-col justify-between h-full border border-gray-200 
                          dark:border-gray-700 dark:hover:border-gray-500 hover:scale-105 transform duration-300">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 truncate">
                        {{ $card['name'] }}
                    </h3>
                    <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-3">
                        {{ $card['count'] }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Optional Dark Mode Styling -->
    <style>
        html {
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        body {
            background-color: #f9fafb;
        }

        .dark body {
            background-color: #0a1120;
        }

        .dark .border-gray-200 {
            border-color: #1e293b !important;
        }

        .dark .hover\:border-gray-500:hover {
            border-color: #475569 !important;
        }

        /* Ensure dashboard cards text wraps nicely on small screens */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</x-app-layout>
