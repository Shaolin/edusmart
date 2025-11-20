<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                Assigned Subjects to Classes Teachers
            </h2>

            <!-- ASSIGN NEW BUTTON -->
            <a href="{{ route('class_subject_teacher.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Assign New
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">

            <!-- FILTERS + SEARCH -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded shadow mb-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <!-- Search -->
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search teacher, subject, class..."
                           class="w-full border-gray-300 rounded dark:bg-gray-700 dark:text-gray-100">

                    <!-- Filter teacher -->
                    <select name="teacher_id"
                            class="w-full border-gray-300 rounded dark:bg-gray-700 dark:text-gray-100">
                        <option value="">All Teachers</option>
                        @foreach ($teachers as $t)
                            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter class -->
                    <select name="class_id"
                            class="w-full border-gray-300 rounded dark:bg-gray-700 dark:text-gray-100">
                        <option value="">All Classes</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter button -->
                    <button type="submit"
                        class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700 w-full">
                        Apply Filters
                    </button>

                </form>
            </div>

            <!-- TABLE -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="px-4 py-2 border">Teacher</th>
                            <th class="px-4 py-2 border">Class</th>
                            <th class="px-4 py-2 border">Subject</th>
                            <th class="px-4 py-2 border">Level</th>
                            <th class="px-4 py-2 border text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assignments as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">

                                <td class="px-4 py-2 border">{{ $a->teacher_name }}</td>
                                <td class="px-4 py-2 border">{{ $a->class_name }}</td>
                                <td class="px-4 py-2 border">{{ $a->subject_name }}</td>
                                <td class="px-4 py-2 border">{{ $a->subject_level }}</td>

                                <!-- ACTION BUTTONS -->
                                <td class="px-4 py-2 border text-center space-x-2">

                                    <!-- EDIT BUTTON -->
                                    <a href="{{ route('class_subject_teacher.edit', $a->id) }}"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                        Edit
                                    </a>

                                    <!-- DELETE BUTTON -->
                                    <form action="{{ route('class_subject_teacher.destroy', $a->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No assignments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $assignments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
