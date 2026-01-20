<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
            <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                Class Attendance
            </h2>
            <span class="px-3 py-1 rounded-lg bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-sm sm:text-base">
                Total Students: {{ $students->count() }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6">

                <!-- FILTERS -->
                <form method="GET" class="mb-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text"
                               name="name"
                               value="{{ request('name') }}"
                               placeholder="Search by student name"
                               class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:text-gray-100 w-full sm:flex-1">

                        <input type="date"
                               name="date"
                               value="{{ request('date') }}"
                               class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:text-gray-100 w-full sm:w-auto">

                        <div class="flex gap-2 sm:gap-3 mt-2 sm:mt-0">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full sm:w-auto">
                                Filter
                            </button>

                            <a href="{{ route('teachers.attendance.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 w-full sm:w-auto">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('teachers.attendance.store') }}">
                    @csrf

                    <!-- DESKTOP TABLE -->
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm">Name</th>
                                <th class="px-4 py-3 text-left text-sm">Admission No</th>
                                <th class="px-4 py-3 text-left text-sm">Status</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-2">{{ $student->name }}</td>
                                    <td class="px-4 py-2">{{ $student->admission_number }}</td>
                                    <td class="px-4 py-2">
                                        <select name="attendance[{{ $student->id }}]"
                                                class="px-2 py-1 border rounded-lg dark:bg-gray-800 dark:text-gray-100">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="text-center py-4 text-gray-500 dark:text-gray-400">
                                        No students found.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE VERSION -->
                    <div class="md:hidden space-y-4">
                        @forelse($students as $student)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow p-4">
                                <p class="text-sm"><strong>Name:</strong> {{ $student->name }}</p>
                                <p class="text-sm"><strong>Admission No:</strong> {{ $student->admission_number }}</p>

                                <select name="attendance[{{ $student->id }}]"
                                        class="mt-2 px-2 py-1 border rounded-lg dark:bg-gray-700 dark:text-gray-100 w-full">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400">No students found.</p>
                        @endforelse
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="mt-4">
                        <button type="submit"
                                class="px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Submit Attendance
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
