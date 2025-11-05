<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">
            Student Results
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        {{-- Filter Section --}}
        <form method="GET" action="{{ route('results.index') }}" class="flex flex-wrap gap-4 mb-6">
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Session</label>
                <select name="session_id" class="border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Sessions</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Term</label>
                <select name="term_id" class="border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Terms</option>
                    @foreach ($terms as $term)
                        <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Class</label>
                <select name="class_id" class="border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Classes</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                    Filter
                </button>
            </div>
        </form>

        {{-- Results Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <th class="p-3 text-left">Student</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Term</th>
                        <th class="p-3 text-left">Session</th>
                        <th class="p-3 text-center">Test</th>
                        <th class="p-3 text-center">Exam</th>
                        <th class="p-3 text-center">Total</th>
                        <th class="p-3 text-center">Grade</th>
                        <th class="p-3 text-center">Remark</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $result)
                        <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="p-3">{{ $result->student->first_name }} {{ $result->student->last_name }}</td>
                            <td class="p-3">{{ $result->subject->name }}</td>
                            <td class="p-3">{{ $result->term->name }}</td>
                            <td class="p-3">{{ $result->session->name }}</td>
                            <td class="p-3 text-center">{{ $result->test_score }}</td>
                            <td class="p-3 text-center">{{ $result->exam_score }}</td>
                            <td class="p-3 text-center font-semibold">{{ $result->total_score }}</td>
                            <td class="p-3 text-center font-semibold">{{ $result->grade }}</td>
                            <td class="p-3 text-center">{{ $result->remark }}</td>
                            <td class="p-3 text-center flex justify-center gap-2">
                                <a href="{{ route('results.edit', $result->id) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm">Edit</a>

                                <form action="{{ route('results.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Delete this result?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-4 text-center text-gray-600 dark:text-gray-300">
                                No results found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
