<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-xl sm:text-2xl font-bold">
                Students in {{ $class->name }}
            </h2>

            <div class="flex flex-wrap gap-2">
                <button id="toggle-dark" 
                        class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded text-sm">
                    Toggle Dark Mode
                </button>

                {{-- 👁️ View Class Results --}}
                <a href="{{ route('results.classRanking', [
                        'class_id' => $class->id,
                        'term_id'  => request('term_id', \App\Models\Term::latest()->first()->id ?? 1),
                        'session_id' => request('session_id', \App\Models\AcademicSession::latest()->first()->id ?? 1)
                    ]) }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-md shadow text-sm font-semibold">
                    👁️ View Class Results
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6 
                text-gray-900 dark:text-gray-100 transition-colors duration-300">

        {{-- 🔽 Term & Session Filters --}}
        <form method="GET" id="filterForm" 
              class="flex flex-col sm:flex-row flex-wrap gap-4 mb-6 items-start sm:items-end">
            
            <div class="w-full sm:w-48">
                <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Select Term</label>
                <select name="term_id" id="term_id"
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100"
                        onchange="document.getElementById('filterForm').submit()">
                    @foreach (\App\Models\Term::all() as $term)
                        <option value="{{ $term->id }}"
                            {{ request('term_id', \App\Models\Term::latest()->first()->id ?? '') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Select Session</label>
                <select name="session_id" id="session_id"
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100"
                        onchange="document.getElementById('filterForm').submit()">
                    @foreach (\App\Models\AcademicSession::all() as $session)
                        <option value="{{ $session->id }}"
                            {{ request('session_id', \App\Models\AcademicSession::latest()->first()->id ?? '') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- 📋 Students Table (Mobile Scrollable) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-sm sm:text-base">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                        <th class="px-4 py-2 text-left whitespace-nowrap">#</th>
                        <th class="px-4 py-2 text-left whitespace-nowrap">Name</th>
                        <th class="px-4 py-2 text-left whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->students as $student)
                        <tr class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 whitespace-nowrap font-medium">
                                {{ $student->name }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-wrap gap-2">
                                    {{-- ✏️ Enter Results --}}
                                    <a href="{{ route('results.createResult', $student->id) }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs sm:text-sm shadow">
                                        ✏️ Enter Results
                                    </a>

                                    {{-- 👁️ View Results --}}
                                    <a href="{{ route('results.view', [
                                            'student_id' => $student->id,
                                            'term_id' => request('term_id', \App\Models\Term::latest()->first()->id ?? 1),
                                            'session_id' => request('session_id', \App\Models\AcademicSession::latest()->first()->id ?? 1)
                                        ]) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-xs sm:text-sm shadow">
                                        👁️ View Result
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
