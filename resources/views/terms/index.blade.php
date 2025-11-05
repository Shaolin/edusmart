<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Terms
             </h2>
           
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto mt-6 space-y-4">
        <!-- Add Term Button -->
        <div class="flex justify-end">
            <a href="{{ route('terms.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                + Add Term
            </a>
        </div>

        <!-- Terms Table -->
        <div class="overflow-x-auto bg-white dark:bg-gray-800 p-4 rounded-lg shadow  transition-colors duration-300">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Session</th>
                        <th class="p-2 text-left">Active</th>
                        <th class="p-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($terms as $term)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="p-2 whitespace-nowrap">{{ $term->name }}</td>
                            <td class="p-2 whitespace-nowrap">{{ $term->session->name ?? 'N/A' }}</td>
                            <td class="p-2 whitespace-nowrap">
                                {!! $term->is_active
                                    ? '<span class="text-green-600 font-semibold">Active</span>'
                                    : '<span class="text-gray-500">Inactive</span>' !!}
                            </td>
                            <td class="p-2 whitespace-nowrap flex flex-wrap gap-2">
                                <a href="{{ route('terms.edit', $term->id) }}" 
                                   class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('terms.destroy', $term->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline"
                                            onclick="return confirm('Delete this term?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($terms->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center p-4 text-gray-500 dark:text-gray-300">
                                No terms found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
