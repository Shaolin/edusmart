<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Add New Academic Session
             </h2>

            
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <form action="{{ route('sessions.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Session Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Session Name (e.g. 2025/2026)
                </label>
                <input 
                    type="text" 
                    name="name" 
                    class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2
                           focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100" 
                    required>
            </div>

            <!-- Active Checkbox -->
            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    id="is_active"
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-200">
                    Set as Active
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow transition">
                Save Session
            </button>
        </form>
    </div>
</x-app-layout>
