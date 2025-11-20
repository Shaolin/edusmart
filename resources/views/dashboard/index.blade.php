<x-app-layout>
 
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 w-full">
    
            <!-- Dashboard Title -->
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                Dashboard Overview
            </h2>
    
            <!-- Right-side: Welcome + Toggle -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
    
                <!-- Welcome Message -->
                <h2 class="font-semibold text-lg sm:text-xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 w-full sm:w-auto text-center sm:text-left">
                    {{-- Welcome back, <span class="font-semibold">{{ auth()->user()->name }}</span>! --}}
                    Welcome back, {{ auth()->user()->name }}
                </h2>
    
             
            </div>
    
        </div>
    </x-slot>
    
    

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <!-- Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- Card Template Example -->
            @php
            $schoolId = auth()->user()->school_id;
        
            $cards = [
    ['name' => 'Students', 'route' => 'students.index', 'count' => $totalStudents ?? 0, 'color' => 'blue'],
    ['name' => 'Teachers', 'route' => 'teachers.index', 'count' => $totalTeachers ?? 0, 'color' => 'green'],
    ['name' => 'Classes', 'route' => 'classes.index', 'count' => $totalClasses ?? 0, 'color' => 'purple'],
    ['name' => 'Guardians', 'route' => 'guardians.index', 'count' => $totalGuardians ?? 0, 'color' => 'yellow'],
    ['name' => 'Fees', 'route' => 'fees.index', 'count' => 'View', 'color' => 'indigo'],
    ['name' => 'Payments', 'route' => 'fee_payments.index', 'count' => 'View', 'color' => 'blue'],
    ['name' => 'School', 'route' => 'schools.index', 'count' => 'Manage', 'color' => 'pink'],
    ['name' => 'Results', 'route' => 'results.selectClass', 'count' => 'Manage', 'color' => 'red'],
    ['name' => 'Subjects', 'route' => 'subjects.index', 'count' => 'Manage', 'color' => 'indigo'],
    ['name' => 'Assigned Subjects', 'route' => 'class_subject_teacher.index', 'count' => 'Manage', 'color' => 'teal'], // ✅ new card
    ['name' => 'Sessions', 'route' => 'sessions.index', 'count' => 'Manage', 'color' => 'indigo'],
    ['name' => 'Terms', 'route' => 'terms.index', 'count' => 'Manage', 'color' => 'orange'],
];

        @endphp
        

          

            @foreach($cards as $card)
    <a href="{{ route($card['route']) }}"
       class="bg-white dark:bg-[#0f172a] shadow rounded-xl p-6 hover:shadow-lg transition flex flex-col justify-between h-full
              dark:border dark:border-gray-700 dark:hover:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $card['name'] }}</h3>
        <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-3">{{ $card['count'] }}</p>
    </a>
@endforeach

        </div>
    </div>

    <!-- 🌗 Dark Mode Script -->
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

    <!-- 🌙 Global Theme Styling -->
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

        /* Extra small screen adjustments */
        @media (max-width: 640px) {
            h2 {
                font-size: 1.25rem;
            }

            .p-6 {
                padding: 1.25rem;
            }
        }
    </style>
</x-app-layout>
