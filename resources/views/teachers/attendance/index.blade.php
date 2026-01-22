<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100">
                Class Attendance
            </h2>

            <span class="px-3 py-1 rounded-lg bg-green-100 dark:bg-green-900
                         text-green-700 dark:text-green-300 text-sm">
                Total Students: {{ $students->count() }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg p-4 sm:p-6">

                {{-- FILTERS --}}
                <form method="GET" class="mb-5">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input
                            type="text"
                            name="name"
                            value="{{ request('name') }}"
                            placeholder="Search by student name"
                            class="px-3 py-2 border rounded-lg w-full sm:flex-1
                                   dark:bg-gray-800 dark:text-gray-100">

                        <input
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="px-3 py-2 border rounded-lg w-full sm:w-auto
                                   dark:bg-gray-800 dark:text-gray-100">

                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Filter
                            </button>

                            <a href="{{ route('teachers.attendance.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-800
                                      dark:text-gray-100 rounded-lg hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('teachers.attendance.store') }}">
                    @csrf
                
                    <div class="space-y-3">
                        @forelse($students as $student)
                            <label
                                class="flex items-center justify-between gap-3 p-3
                                       bg-gray-50 dark:bg-gray-800 rounded-lg
                                       border border-gray-200 dark:border-gray-700
                                       hover:bg-gray-100 dark:hover:bg-gray-700">
                
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $student->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Admission No: {{ $student->admission_number }}
                                    </p>
                                </div>
                
                                <input
                                    type="checkbox"
                                    name="attendance[]"
                                    value="{{ $student->id }}"
                                    class="w-6 h-6 text-green-600 rounded focus:ring-green-500">
                            </label>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400">
                                No students found.
                            </p>
                        @endforelse
                    </div>
                
                    <div class="mt-6">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-green-600
                                   text-white rounded-lg hover:bg-green-700">
                            Submit Attendance
                        </button>
                    </div>
                </form>
                

            </div>
        </div>
    </div>
</x-app-layout>
