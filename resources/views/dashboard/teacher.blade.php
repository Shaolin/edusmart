<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">

            <!-- Dashboard Title -->
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 
                       bg-gradient-to-r from-gray-100 to-gray-200 
                       dark:from-gray-900 dark:to-gray-800 
                       text-gray-900 dark:text-gray-100 shadow-sm 
                       border border-gray-300 dark:border-gray-700 
                       transition-all duration-300 w-full sm:w-auto text-center sm:text-left">
                Teacher Dashboard
            </h2>

            <!-- Welcome Message -->
            <h2 class="font-semibold text-lg sm:text-xl rounded-lg px-4 py-2
                       bg-gradient-to-r from-gray-100 to-gray-200 
                       dark:from-gray-900 dark:to-gray-800 
                       text-gray-900 dark:text-gray-100 shadow-sm
                       border border-gray-300 dark:border-gray-700
                       transition-all duration-300 w-full sm:w-auto text-center sm:text-left">
                Welcome back, {{ auth()->user()->name }}
            </h2>
        </div>
    </x-slot>

    <!-- Dashboard Cards -->
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            @php
               
    $cards = [
        ['name' => 'Students',   'route' => 'teachers.students',       'color' => 'blue'],
        ['name' => 'Results',    'route' => 'teachers.results.index',  'color' => 'red'],
        ['name' => 'Attendance', 'route' => 'teachers.attendance.index','color' => 'green'], // New card
    ];
@endphp

        

            @foreach($cards as $card)
                <a href="{{ $card['route'] !== '#' ? route($card['route']) : '#' }}"
                   class="relative bg-white dark:bg-[#0f172a] rounded-2xl p-6 h-36 
                          flex items-center justify-center shadow-md hover:shadow-xl 
                          transition-all duration-300 border border-gray-200 
                          dark:border-gray-700 dark:hover:border-{{ $card['color'] }}-400
                          hover:border-{{ $card['color'] }}-400 group">

                    <!-- Glow Effect for Dark Mode -->
                    <div class="absolute inset-0 rounded-2xl opacity-0 
                                group-hover:opacity-20 blur-xl 
                                bg-{{ $card['color'] }}-400 dark:bg-{{ $card['color'] }}-500 
                                transition-all duration-300">
                    </div>

                    <h3 class="text-2xl font-semibold relative z-10 
                               text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400">
                        {{ $card['name'] }}
                    </h3>
                </a>
            @endforeach

        </div>
    </div>

    <style>
        html { transition: background-color 0.4s ease, color 0.4s ease; }
        body { background-color: #f9fafb; }
        .dark body { background-color: #0a1120; }

        /* Improve dark mode borders */
        .dark .border-gray-200 { border-color: #1e293b !important; }
        .dark .hover\:border-gray-500:hover { border-color: #475569 !important; }

        /* Extra glow on hover */
        .card-glow:hover {
            box-shadow: 0px 0px 12px rgba(255,255,255,0.3);
        }
    </style>
</x-app-layout>
