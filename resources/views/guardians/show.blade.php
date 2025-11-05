<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Guardian Details
            </h2>
          
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-6 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <!-- Guardian Info -->
                <h3 class="text-lg font-semibold mb-4">Guardian Information</h3>
                <div class="grid gap-3 grid-cols-1 md:grid-cols-2">
                    <div><strong class="font-medium">Name:</strong> {{ $guardian->name }}</div>
                    <div><strong class="font-medium">Phone:</strong> {{ $guardian->phone }}</div>
                    <div><strong class="font-medium">Email:</strong> {{ $guardian->email ?? '-' }}</div>
                </div>

                <!-- Students -->
                <h3 class="text-lg font-semibold mt-6 mb-3">Students Under this Guardian</h3>
                @if($guardian->students->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">No students registered under this guardian.</p>
                @else
                    <ul class="space-y-2 mt-2">
                        @foreach($guardian->students as $student)
                            <li class="bg-gray-50 dark:bg-gray-800 p-3 rounded border dark:border-gray-700">
                                <span class="font-semibold">{{ $student->name }}</span> 
                                ({{ $student->admission_number }}) 
                                • Class: {{ $student->schoolclass->name ?? '-' }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <!-- Actions -->
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('guardians.edit', $guardian->id) }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-center">
                        Edit
                    </a>

                    <form action="{{ route('guardians.destroy', $guardian->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this guardian?')">
                            Delete
                        </button>
                    </form>

                    <a href="{{ route('guardians.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-center">
                        Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
