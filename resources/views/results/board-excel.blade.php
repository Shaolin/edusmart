<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    📊 Board Result Export
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Generate student results in the format required by the Education Board.
                </p>
            </div>

            <a href="{{ url()->previous() }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-4xl mx-auto px-4">

            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Generate Board Excel File
                    </h3>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Select the class, term, session and subject to generate the required Excel file.
                    </p>
                </div>

                <form method="GET"
                      action="{{ route('results.board.excel', $class->id) }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Class --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class
                            </label>

                            <select name="class_id"
                                    disabled
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700
                                           dark:bg-gray-800 dark:text-white">
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            </select>

                            <p class="text-xs text-gray-500 mt-1">
                                Current class: {{ $class->name }}
                            </p>
                        </div>

                        {{-- Term --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Term
                            </label>

                            <select name="term_id"
                                    required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700
                                           dark:bg-gray-800 dark:text-white">

                                <option value="">Select Term</option>

                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}"
                                        {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Session --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Session
                            </label>

                            <select name="session_id"
                                    required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700
                                           dark:bg-gray-800 dark:text-white">

                                <option value="">Select Session</option>

                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}"
                                        {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject
                            </label>

                            <select name="subject_id"
                                    required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700
                                           dark:bg-gray-800 dark:text-white">

                                <option value="">Select Subject</option>

                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end">

                        <button type="submit"
                                class="inline-flex items-center px-5 py-3
                                       bg-green-600 hover:bg-green-700
                                       text-white font-semibold rounded-md shadow">

                            📥 Generate Excel

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>