<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Add New Subject
             </h2>

          
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">

        <form action="{{ route('subjects.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Subject Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Subject Name
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100
                           focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter subject name, e.g. Mathematics"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Level --}}
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Level
                </label>
                <select
                    id="level"
                    name="level"
                    class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100
                           focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
                    <option value="">-- Select Level --</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
                @error('level')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('subjects.index') }}"
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-md
                           hover:bg-gray-400 dark:hover:bg-gray-500 transition text-center">
                    Cancel
                </a>

                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow text-center">
                    Save Subject
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
