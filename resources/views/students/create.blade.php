<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900  rounded-lg px-4 py-2  text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center  w-full">
                Add New Student
                
            </h2>

           
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Student Name -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Student Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Admission Number -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Admission Number</label>
                        <input type="text" name="admission_number" value="{{ old('admission_number') }}" 
                               class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        @error('admission_number') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Gender</label>
                        <select name="gender" 
                                class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Class</label>
                        <select name="class_id" 
                                class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Guardian -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Guardian</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <select name="guardian_id" id="guardian-select" 
                                    class="flex-1 px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select Guardian</option>
                                @foreach($guardians as $guardian)
                                    <option value="{{ $guardian->id }}" {{ old('guardian_id')==$guardian->id ? 'selected' : '' }}>
                                        {{ $guardian->name }} ({{ $guardian->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('guardian_id') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                      <!-- Submit -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Add Student
                    </button>
                    <a href="{{ route('students.index') }}"
                        class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        Back
                    </a>
                </div>

                    <!-- Submit -->
                    {{-- <div class="pt-4">
                        <button type="submit" 
                                class="w-full sm:w-auto px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                            Add Student
                        </button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Dark Mode
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
