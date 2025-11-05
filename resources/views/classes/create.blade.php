<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
           
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Add New Class
            </h2>
          
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 font-sans 
                        bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 
                        transition-colors duration-300">

                <form action="{{ route('classes.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Class Name --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Class Name
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring 
                               dark:bg-gray-700 dark:text-gray-100" required>
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Section --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Section <span class="text-xs text-gray-500">(optional)</span>
                        </label>
                        <input type="text" name="section" value="{{ old('section') }}"
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-100">
                        @error('section') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Form Teacher --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Assign Form Teacher <span class="text-xs text-gray-500">(optional)</span>
                        </label>
                        <select name="form_teacher_id"
                                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('form_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('form_teacher_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Next Class --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Next Class (for promotion)
                        </label>
                        <select name="next_class_id"
                                class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="">-- None (Final Class) --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('next_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                        @error('next_class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-3">
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full sm:w-auto">
                            Add Class
                        </button>
                        <a href="{{ route('classes.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-center w-full sm:w-auto">
                            Back
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if(localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
