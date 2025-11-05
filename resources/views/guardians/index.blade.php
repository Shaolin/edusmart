<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
         
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Guardians
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('guardians.create') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm sm:text-base">
                    Add Guardian
                </a>
              
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                {{-- Responsive Table/Card --}}
                <table class="w-full border-collapse hidden md:table">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guardians as $guardian)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $guardian->name }}</td>
                                <td class="px-4 py-2">{{ $guardian->phone }}</td>
                                <td class="px-4 py-2">{{ $guardian->email ?? '-' }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    <a href="{{ route('guardians.show', $guardian->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">View</a>
                                    <a href="{{ route('guardians.edit', $guardian->id) }}" class="px-2 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Edit</a>
                                    <form action="{{ route('guardians.destroy', $guardian->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                            class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                                            onclick="return confirm('Are you sure you want to delete this guardian?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-2 text-center">No guardians found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Mobile Card View --}}
                <div class="md:hidden space-y-3">
                    @forelse($guardians as $guardian)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <p><span class="font-semibold">Name:</span> {{ $guardian->name }}</p>
                            <p><span class="font-semibold">Phone:</span> {{ $guardian->phone }}</p>
                            <p><span class="font-semibold">Email:</span> {{ $guardian->email ?? '-' }}</p>

                            <div class="flex flex-wrap gap-2 mt-3">
                                <a href="{{ route('guardians.show', $guardian->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">View</a>
                                <a href="{{ route('guardians.edit', $guardian->id) }}" class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Edit</a>
                                <form action="{{ route('guardians.destroy', $guardian->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                                            onclick="return confirm('Are you sure you want to delete this guardian?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-4">No guardians found.</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $guardians->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if(localStorage.getItem('dark-mode') === 'true') {
            htmlEl.classList.add('dark');
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
