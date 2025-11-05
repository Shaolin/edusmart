<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            
            <h2 class="font-semibold text-2xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Add New Teacher
            </h2>

           
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div
            class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6
                   text-gray-900 dark:text-gray-100 font-sans transition-colors duration-300">

            <form action="{{ route('teachers.store') }}" method="POST">
                @csrf

                <!-- User Info -->
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                    User Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" required>
                    </div>
                </div>

                <!-- Teacher Info -->
                <h3 class="text-lg font-semibold mt-8 mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                    Teacher Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Staff ID</label>
                        <input type="text" name="staff_id" value="{{ old('staff_id') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                        @error('staff_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                        @error('qualification') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                        @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Add Teacher
                    </button>
                    <a href="{{ route('teachers.index') }}"
                        class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        Back
                    </a>
                </div>

            </form>

        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
