
<x-app-layout>
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Inactive Students
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                These students are no longer active, but their historical records are retained.
            </p>
        </div>

        <a href="{{ route('students.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            Back to Students
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-md bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Admission No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Student Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Class</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Guardian</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($students as $student)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            {{ $student->admission_number }}
                        </td>

                        <td class="px-4 py-3 text-sm">
                            {{ $student->name }}
                        </td>

                        <td class="px-4 py-3 text-sm">
                            {{ $student->schoolClass->name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-3 text-sm">
                            {{ $student->guardian->name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <a href="{{ route('students.show', $student->id) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline mr-3">
                                View Records
                            </a>

                            <form action="{{ route('students.reactivate', $student->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="text-green-600 dark:text-green-400 hover:underline"
                                        onclick="return confirm('Reactivate this student?')">
                                    Reactivate
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No inactive students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>

</div>
</x-app-layout>
