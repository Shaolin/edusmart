<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Edit Academic Session
             </h2>

           
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <form action="{{ route('sessions.update', $session->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Session Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Session Name
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $session->name) }}"
                    class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                           focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    id="is_active" 
                    value="1"
                    {{ $session->is_active ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-200">
                    Set as Active Session
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow transition">
                    Update Session
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
