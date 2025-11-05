<x-app-layout>
    <x-slot name="header">
       
        <div class="flex justify-between items-center flex-wrap gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Select Class
             </h2>
          
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($classes as $class)
            <a href="{{ route('results.showStudents', $class->id) }}"
               class="block bg-white dark:bg-gray-800 p-5 rounded-xl shadow 
                      hover:shadow-md transition-all duration-200 
                      hover:bg-blue-50 dark:hover:bg-gray-700 hover:-translate-y-1">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ $class->name }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                    Section: {{ $class->section ?? '—' }}
                </p>
            </a>
        @endforeach
    </div>
</x-app-layout>
