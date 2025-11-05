<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                Teacher Dashboard
            </h2>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <h2 class="font-semibold text-lg sm:text-xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                    Welcome back, {{ auth()->user()->name }}
                </h2>

               
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            @php
                $teacher = auth()->user()->teacher;
                $class = $teacher->schoolClass;
                $cards = [
                    ['name' => 'Students', 'route' => 'teacher.students', 'count' => $class ? $class->students->count() : 0, 'color' => 'blue'],
                    ['name' => 'Results', 'route' => 'teacher.results', 'count' => 'Manage', 'color' => 'red'],
                    ['name' => 'Subjects', 'route' => 'subjects.index', 'count' => 'View', 'color' => 'indigo'],
                    ['name' => 'Sessions', 'route' => 'sessions.index', 'count' => 'View', 'color' => 'green'],
                    ['name' => 'Terms', 'route' => 'terms.index', 'count' => 'View', 'color' => 'yellow'],
                ];
            @endphp

            @foreach($cards as $card)
                <a href="{{ route($card['route']) }}"
                   class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $card['name'] }}</h3>
                    <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-3">{{ $card['count'] }}</p>
                </a>
            @endforeach
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') {
            htmlEl.classList.add('dark');
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
