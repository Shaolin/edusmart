<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                Teacher Dashboard
            </h2>

            <button id="toggle-dark"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg transition-colors duration-300 shadow-sm text-sm sm:text-base w-full sm:w-auto text-center">
                Toggle Dark Mode
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- Students Card -->
            <a href="{{ route('teachers.students') }}"
               class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Students</h3>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-3">{{ $totalStudents ?? 0 }}</p>
            </a>

            <!-- Results Card -->
            {{-- <a href="{{ route('teachers.results') }}"
               class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Results</h3>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-3">View / Edit</p>
            </a> --}}

            <!-- Subjects Card (view only) -->
            <a href="{{ route('subjects.index') }}"
               class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Subjects</h3>
                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-3">View</p>
            </a>

            <!-- Terms & Sessions Card (view only) -->
            <a href="{{ route('terms.index') }}"
               class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Terms & Sessions</h3>
                <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-3">View</p>
            </a>

        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if(localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
