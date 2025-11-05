<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
           
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Classes
            </h2>

            <div class="flex flex-col sm:flex-row gap-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('classes.create') }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-center">
                        Add Class
                    </a>
                @endif

            </div>
        </div>
    </x-slot>

    <div class="py-6 px-3 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                <!-- Table Converts to Cards on Mobile -->
                <div class="overflow-x-auto hidden md:block">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-2 text-left">Class Name</th>
                                <th class="px-4 py-2 text-left">Section</th>
                                <th class="px-4 py-2 text-left">Form Teacher</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $class->name }}</td>
                                    <td class="px-4 py-2">{{ $class->section ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $class->formTeacher->user->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-2 space-x-1">
                                        <a href="{{ route('classes.show', $class->id) }}" 
                                           class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            View
                                        </a>

                                        @if(auth()->user()->role === 'admin')
                                            <a href="{{ route('classes.edit', $class->id) }}" 
                                               class="px-2 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                                Edit
                                            </a>

                                            <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                                        onclick="return confirm('Are you sure you want to delete this class?')">
                                                    Delete
                                                </button>
                                            </form>

                                            <form action="{{ route('classes.promote', $class->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Promote all students in {{ $class->name }} {{ $class->section }}?')"
                                                        class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                                    Promote
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center">No classes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    @forelse($classes as $class)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 shadow-sm">
                            <p><strong>Class:</strong> {{ $class->name }}</p>
                            <p><strong>Section:</strong> {{ $class->section ?? '-' }}</p>
                            <p><strong>Form Teacher:</strong> {{ $class->formTeacher->user->name ?? 'Unassigned' }}</p>

                            <div class="mt-3 flex flex-col gap-2">
                                <a href="{{ route('classes.show', $class->id) }}" 
                                   class="px-3 py-2 bg-blue-600 text-white rounded text-center hover:bg-blue-700">
                                   View
                                </a>

                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('classes.edit', $class->id) }}" 
                                       class="px-3 py-2 bg-yellow-600 text-white rounded text-center hover:bg-yellow-700">
                                       Edit
                                    </a>

                                    <form action="{{ route('classes.destroy', $class->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-2 bg-red-600 text-white rounded w-full hover:bg-red-700"
                                                onclick="return confirm('Are you sure you want to delete this class?')">
                                            Delete
                                        </button>
                                    </form>

                                    <form action="{{ route('classes.promote', $class->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-2 bg-green-600 text-white rounded w-full hover:bg-green-700"
                                                onclick="return confirm('Promote all students in {{ $class->name }} {{ $class->section }}?')">
                                            Promote
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">No classes found.</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $classes->links() }}
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
