<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Edit Teacher
            </h2>
           
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 text-gray-900 dark:text-gray-100 transition">

                <form action="{{ route('teachers.update', $teacher->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- User Info -->
                    <h3 class="text-lg font-semibold mb-4 border-l-4 border-yellow-600 pl-2">User Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Name</label>
                            <input type="text" name="name" value="{{ old('name', $teacher->user->name) }}"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" name="email" value="{{ old('email', $teacher->user->email) }}"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Password</label>
                            <input type="password" name="password" autocomplete="new-password"
                                placeholder="Leave blank to keep current"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Confirm Password</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    <!-- Teacher Info -->
                    <h3 class="text-lg font-semibold mt-8 mb-4 border-l-4 border-yellow-600 pl-2">Teacher Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Staff ID</label>
                            <input type="text" name="staff_id" value="{{ old('staff_id', $teacher->staff_id) }}"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                            @error('staff_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-200">Qualification</label>
                            <input type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                            @error('qualification') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 dark:text-gray-200">Specialization</label>
                            <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                                class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                            @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            Update Teacher
                        </button>
                        <a href="{{ route('teachers.index') }}" 
                            class="px-6 py-2 bg-gray-500 text-white text-center rounded hover:bg-gray-600">
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

        if (localStorage.getItem('dark-mode') === 'true') {
            htmlEl.classList.add('dark');
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
