<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Add Term
             </h2>
           
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow transition-colors duration-300">
        <form action="{{ route('terms.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Term Name -->
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Term Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 p-2"
                       placeholder="e.g. First Term">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Session -->
            <div>
                <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Session</label>
                <select name="session_id" 
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 p-2">
                    <option value="">-- Select Session --</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
                @error('session_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Checkbox -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-200">Set as Active</label>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow transition">
                    Save Term
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
