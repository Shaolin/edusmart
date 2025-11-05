<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Teacher Details
            </h2>
           
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 text-gray-900 dark:text-gray-100 transition">

                <!-- User Info -->
                <h3 class="text-lg font-semibold mb-4 border-l-4 border-yellow-600 pl-2">User Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><strong>Name:</strong> {{ $teacher->user->name }}</div>
                    <div><strong>Email:</strong> {{ $teacher->user->email }}</div>
                </div>

                <!-- Teacher Info -->
                <h3 class="text-lg font-semibold mt-8 mb-4 border-l-4 border-yellow-600 pl-2">Teacher Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><strong>Staff ID:</strong> {{ $teacher->staff_id ?? '-' }}</div>
                    <div><strong>Qualification:</strong> {{ $teacher->qualification ?? '-' }}</div>
                    <div class="sm:col-span-2"><strong>Specialization:</strong> {{ $teacher->specialization ?? '-' }}</div>
                </div>

                <!-- Classes & Subjects -->
                <h3 class="text-lg font-semibold mt-8 mb-4 border-l-4 border-yellow-600 pl-2">Classes & Subjects</h3>

                @if($teacher->formClasses->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No classes assigned yet.</p>
                @else
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($teacher->formClasses as $class)
                            <li>
                                <span class="font-medium">{{ $class->name }}</span>
                                @php
                                    $subjects = $class->subjects->where('pivot.teacher_id', $teacher->id)->pluck('name')->join(', ');
                                @endphp
                                @if($subjects)
                                    <span class="text-sm text-gray-600 dark:text-gray-300"> — Subjects: {{ $subjects }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                <!-- Actions -->
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('teachers.edit', $teacher->id) }}" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-center">
                        Edit
                    </a>

                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" 
                          class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 w-full sm:w-auto"
                            onclick="return confirm('Are you sure you want to delete this teacher?')">
                            Delete
                        </button>
                    </form>

                    <a href="{{ route('teachers.index') }}" 
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-center">
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

        if (localStorage.getItem('dark-mode') === 'true') {
            htmlEl.classList.add('dark');
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
