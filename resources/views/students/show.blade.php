<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <h2
                class="font-semibold text-xl text-gray-900 dark:text-gray-100 dark:bg-gray-900 rounded-lg px-4 py-2 w-full sm:w-auto text-center transition-colors duration-300">
                Student Details
            </h2>
          
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div
            class="max-w-3xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">

            <!-- Student Info -->
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-300 dark:border-gray-700 pb-2">Student Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><strong>Name:</strong> {{ $student->name }}</div>
                <div><strong>Admission No:</strong> {{ $student->admission_number }}</div>
                <div><strong>Gender:</strong> {{ ucfirst($student->gender) }}</div>
                <div><strong>Class:</strong> {{ $student->schoolClass->name ?? '-' }}</div>
            </div>

            <!-- Guardian Info -->
            <h3 class="text-lg font-semibold mt-8 mb-4 border-b border-gray-300 dark:border-gray-700 pb-2">Guardian Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><strong>Name:</strong> {{ $student->guardian->name ?? '-' }}</div>
                <div><strong>Phone:</strong> {{ $student->guardian->phone ?? '-' }}</div>
                <div class="sm:col-span-2"><strong>Email:</strong> {{ $student->guardian->email ?? '-' }}</div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-wrap gap-3 justify-center sm:justify-start">
                <a href="{{ route('students.edit', $student->id) }}"
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                    Edit
                </a>

                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                        onclick="return confirm('Are you sure you want to delete this student?')">
                        Delete
                    </button>
                </form>

                <a href="{{ route('students.index') }}"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                    Back to List
                </a>
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
