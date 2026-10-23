
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <h2 class="font-semibold text-2xl rounded-lg px-4 py-2
                       text-gray-900 dark:text-gray-100
                       dark:bg-gray-900 transition-colors duration-300">
                Promote Students — {{ $class->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6
                        text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Select the destination class for each student below.
                    You can assign students to different classes as needed.
                </p>

                {{-- Success message --}}
                @if (session('success'))
                    <div class="mb-4 p-3 rounded
                                bg-green-100 text-green-800
                                dark:bg-green-900/40 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- General error message --}}
                @if (session('error'))
                    <div class="mb-4 p-3 rounded
                                bg-red-100 text-red-800
                                dark:bg-red-900/40 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded
                                bg-red-100 text-red-800
                                dark:bg-red-900/40 dark:text-red-300">
                        <strong>Please correct the following errors:</strong>
                        <ul class="list-disc ml-5 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($students->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">
                        There are no active students in {{ $class->name }} to promote.
                    </p>
                @else
                    <form method="POST"
                          action="{{ route('classes.process-manual-promotion', $class->id) }}">
                        @csrf

                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200
                                          dark:border-gray-700 text-sm">

                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800
                                               text-gray-700 dark:text-gray-200">
                                        <th class="px-4 py-3 text-left border border-gray-200 dark:border-gray-700">
                                            S/N
                                        </th>
                                        <th class="px-4 py-3 text-left border border-gray-200 dark:border-gray-700">
                                            Student Name
                                        </th>
                                        <th class="px-4 py-3 text-left border border-gray-200 dark:border-gray-700">
                                            Destination Class
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($students as $student)
                                        <tr class="border-b border-gray-200 dark:border-gray-700
                                                   hover:bg-gray-50 dark:hover:bg-gray-800/70">

                                            <td class="px-4 py-3 border border-gray-200 dark:border-gray-700">
                                                {{ $loop->iteration }}
                                            </td>

                                            <td class="px-4 py-3 border border-gray-200 dark:border-gray-700">
                                                {{ $student->name }}
                                            </td>

                                            <td class="px-4 py-3 border border-gray-200 dark:border-gray-700">
                                                <select
                                                    name="students[{{ $student->id }}]"
                                                    class="w-full rounded border border-gray-300
                                                           bg-white text-gray-900
                                                           dark:bg-gray-700 dark:text-gray-100
                                                           dark:border-gray-600
                                                           focus:ring-2 focus:ring-blue-500
                                                           focus:border-blue-500"
                                                    required>

                                                    <option value="">Select destination</option>

                                                    @foreach ($nextClasses as $nextClass)
                                                        <option
                                                            value="{{ $nextClass->id }}"
                                                            {{ old('students.'.$student->id) == $nextClass->id ? 'selected' : '' }}>
                                                            {{ $nextClass->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <button type="submit"
                                    class="px-5 py-2 bg-blue-600 text-white rounded
                                           hover:bg-blue-700 transition-colors">
                                Confirm Promotion
                            </button>

                            <a href="{{ route('classes.index') }}"
                               class="px-5 py-2 bg-gray-500 text-white rounded
                                      hover:bg-gray-600 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
