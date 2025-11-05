<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
           

            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Edit School Information
             </h2>
           
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 transition duration-300">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 p-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200 p-3 rounded mb-4 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- UPDATE FORM --}}
            <form action="{{ route('schools.update', $school->id) }}" 
                  method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Logo Preview + Upload --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" 
                             alt="Logo" class="h-20 w-20 object-contain rounded shadow mx-auto sm:mx-0">
                    @else
                        <div class="h-20 w-20 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded mx-auto sm:mx-0">
                            <span class="text-gray-500 dark:text-gray-300 text-xs">No Logo</span>
                        </div>
                    @endif

                    <div class="w-full">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Change Logo</label>
                        <input type="file" name="logo" accept="image/*"
                            class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                            dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>

                {{-- School Name --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">School Name</label>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" required
                        class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                        dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                        class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                        dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}"
                        class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                        dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                {{-- Website --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Website</label>
                    <input type="text" name="website" value="{{ old('website', $school->website) }}"
                        class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                        dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                {{-- Address --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 
                        dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('address', $school->address) }}</textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap justify-between gap-3 pt-2">
                    <a href="{{ route('schools.index') }}" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm text-center">
                        Back
                    </a>

                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Update
                    </button>
                </div>
            </form>

            {{-- DELETE BUTTON OUTSIDE THE UPDATE FORM --}}
            <form action="{{ route('schools.destroy', $school->id) }}" method="POST" 
                onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.')"
                class="mt-6 flex justify-end">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                    Delete
                </button>
            </form>

        </div>
    </div>

    {{-- Dark Mode Toggle --}}
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
