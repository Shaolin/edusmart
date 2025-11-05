<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Subjects
            </h2>

         
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto mt-6">

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Add Subject & Search --}}
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <a href="{{ route('subjects.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                + Add Subject
            </a>

            <form method="GET" class="flex gap-2 items-center">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search subjects..."
                       class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                    Search
                </button>
                @if(!empty($search))
                    <a href="{{ route('subjects.index') }}"
                       class="ml-2 text-gray-600 dark:text-gray-300 hover:underline">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden p-4 sm:p-6">

            {{-- Desktop Table --}}
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">#</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Subject Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Level</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($subjects as $index => $subject)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $subjects->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $colors = [
                                            'Nursery' => 'bg-pink-100 text-pink-800',
                                            'Primary' => 'bg-blue-100 text-blue-800',
                                            'JSS' => 'bg-green-100 text-green-800',
                                            'SSS' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$subject->level] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $subject->level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right space-x-3">
                                    <a href="{{ route('subjects.edit', $subject->id) }}"
                                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this subject?')"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                                    No subjects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-4">
                @forelse ($subjects as $index => $subject)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $subject->name }}
                            </h3>
                            <span class="text-xs text-gray-500 dark:text-gray-300">#{{ $subjects->firstItem() + $index }}</span>
                        </div>

                        @php
                            $colors = [
                                'Nursery' => 'bg-pink-100 text-pink-800',
                                'Primary' => 'bg-blue-100 text-blue-800',
                                'JSS' => 'bg-green-100 text-green-800',
                                'SSS' => 'bg-yellow-100 text-yellow-800',
                            ];
                        @endphp

                        <p class="mt-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$subject->level] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $subject->level }}
                            </span>
                        </p>

                        <div class="flex justify-end space-x-4 mt-3">
                            <a href="{{ route('subjects.edit', $subject->id) }}"
                               class="text-indigo-600 dark:text-indigo-300 font-medium text-sm">
                                Edit
                            </a>
                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Delete this subject?')"
                                        class="text-red-600 dark:text-red-300 font-medium text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-300 py-6">No subjects found.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-2 sm:mb-0">
                    Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() }} subjects
                </p>
                <div>
                    {{ $subjects->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Dark Mode Script --}}
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
