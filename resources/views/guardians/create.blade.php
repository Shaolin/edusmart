<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
               Add New Guardians
            </h2>

           
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg p-4 sm:p-6 
                        text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <form action="{{ route('guardians.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500"
                            required>
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-green-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Add Guardian
                        </button>
                        <a href="{{ route('guardians.index') }}" 
                            class="w-full sm:w-auto px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-center">
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
