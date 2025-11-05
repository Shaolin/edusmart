<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
          
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Teachers
            </h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('teachers.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Add Teacher
                </a>

              
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6
                    text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Staff ID</th>
                            <th class="px-4 py-2 text-left">Qualification</th>
                            <th class="px-4 py-2 text-left">Specialization</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $teacher->user->name }}</td>
                                <td class="px-4 py-2">{{ $teacher->user->email }}</td>
                                <td class="px-4 py-2">{{ $teacher->staff_id ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $teacher->qualification ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $teacher->specialization ?? '-' }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    <a href="{{ route('teachers.show', $teacher->id) }}"
                                        class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">View</a>
                                    <a href="{{ route('teachers.edit', $teacher->id) }}"
                                        class="px-2 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">Edit</a>
                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                            onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-2 text-center">No teachers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @forelse($teachers as $teacher)
                    <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4">
                        <p><strong>Name:</strong> {{ $teacher->user->name }}</p>
                        <p><strong>Email:</strong> {{ $teacher->user->email }}</p>
                        <p><strong>Staff ID:</strong> {{ $teacher->staff_id ?? '-' }}</p>
                        <p><strong>Qualification:</strong> {{ $teacher->qualification ?? '-' }}</p>
                        <p><strong>Specialization:</strong> {{ $teacher->specialization ?? '-' }}</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('teachers.show', $teacher->id) }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">View</a>
                            <a href="{{ route('teachers.edit', $teacher->id) }}"
                                class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Edit</a>
                            <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-2">No teachers found.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $teachers->links() }}
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
