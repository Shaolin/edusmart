<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Academic Sessions
             </h2>

           
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-6 space-y-4">

        {{-- Add New Session Button --}}
        <div class="flex justify-end">
            <a href="{{ route('sessions.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm sm:text-base shadow">
                + Add New Session
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-800 dark:text-gray-200">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Session</th>
                            <th class="px-4 py-3 font-semibold">Active</th>
                            <th class="px-4 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($sessions as $session)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3">{{ $session->name }}</td>
                                <td class="px-4 py-3">
                                    @if($session->is_active)
                                        <span class="text-green-600 font-semibold">Yes</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center flex justify-center gap-3 flex-wrap">
                                    <a href="{{ route('sessions.edit', $session) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                        Edit
                                    </a>

                                    <form action="{{ route('sessions.destroy', $session) }}" method="POST"
                                          onsubmit="return confirm('Delete this session?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</x-app-layout>
