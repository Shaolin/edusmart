<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 sm:text-left text-center sm:w-auto">
                Add New School
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

            {{-- Form --}}
            <form action="{{ route('schools.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- School Name --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">School Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter school name" required
                        class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                               focus:ring-blue-500 focus:border-blue-500 transition text-sm px-3 py-2">
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Logo</label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                               text-sm px-3 py-2">
                </div>

                {{-- Contact Info Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g +234 801 234 5678"
                               class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                                      focus:ring-blue-500 focus:border-blue-500 transition text-sm px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g example@school.com"
                               class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                                      focus:ring-blue-500 focus:border-blue-500 transition text-sm px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}" placeholder="e.g https://www.school.com"
                               class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                                      focus:ring-blue-500 focus:border-blue-500 transition text-sm px-3 py-2">
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Address</label>
                    <textarea name="address" rows="3" placeholder="Enter school address"
                              class="w-full mt-2 rounded-lg border-2 border-gray-300 dark:border-white dark:bg-gray-900 dark:text-gray-200
                                     focus:ring-blue-500 focus:border-blue-500 transition text-sm px-3 py-2">{{ old('address') }}</textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                    <a href="{{ route('schools.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-center text-sm transition">
                        Cancel
                    </a>

                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm transition">
                        Save School
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
