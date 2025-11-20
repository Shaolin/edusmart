<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
            <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                Manage Results
            </h2>
            <span class="px-3 py-1 rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-sm sm:text-base">
                Total Students: {{ $totalStudents ?? 0 }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6">

                <!-- FILTER FORM -->
                <form method="GET" action="{{ route('teachers.results.index') }}" class="mb-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by student name"
                               class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:text-gray-100 w-full sm:flex-1">
                        <div class="flex gap-2 sm:gap-3 mt-2 sm:mt-0">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full sm:w-auto">
                                Filter
                            </button>
                            <a href="{{ route('teachers.results.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 w-full sm:w-auto">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- DESKTOP TABLE -->
                <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm sm:text-base">Name</th>
                            <th class="px-4 py-3 text-left text-sm sm:text-base">Admission No</th>
                            <th class="px-4 py-3 text-left text-sm sm:text-base">Class</th>
                            <th class="px-4 py-3 text-left text-sm sm:text-base">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-2 text-sm sm:text-base">{{ $student->name }}</td>
                                <td class="px-4 py-2 text-sm sm:text-base">{{ $student->admission_number }}</td>
                                <td class="px-4 py-2 text-sm sm:text-base">{{ $student->schoolClass->name ?? '-' }}</td>
                                <td class="px-4 py-2 flex flex-col sm:flex-row gap-2 sm:gap-4 text-sm sm:text-base">
                                    <a href="{{ route('teachers.results.edit', $student->id) }}"
                                       class="text-blue-600 hover:underline">
                                        Manage Results
                                    </a>
                                    <a href="{{ route('teachers.results.show', $student->id) }}"
                                       class="text-green-600 hover:underline">
                                        View Result
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm sm:text-base">
                                    No students found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD LAYOUT -->
                <div class="md:hidden space-y-4">
                    @forelse($students as $student)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Name:</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $student->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Admission No:</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $student->admission_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Class:</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $student->schoolClass->name ?? '-' }}</span>
                            </div>
                            <div class="flex gap-3 mt-2">
                                <a href="{{ route('teachers.results.edit', $student->id) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Manage Results
                                </a>
                                <a href="{{ route('teachers.results.show', $student->id) }}"
                                   class="text-green-600 hover:underline text-sm">
                                    View Result
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                            No students found.
                        </div>
                    @endforelse
                </div>

                <!-- PAGINATION -->
                <div class="mt-4">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
