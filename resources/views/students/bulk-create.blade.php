<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100">
            Bulk Student Registration
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg text-sm mb-3">
        {{ session('success') }}
    </div>
@endif

@if(session('importErrors') && count(session('importErrors')))
    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm mb-3">
        <p class="font-semibold mb-1">Some rows were skipped:</p>

        <ul class="list-disc pl-5 space-y-1">
            @foreach(session('importErrors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


                <form action="{{ route('students.bulkStore') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Instructions -->
                    <div class="bg-blue-50 dark:bg-gray-800 p-3 rounded-lg text-sm">
                        <p class="font-semibold mb-1">Format:</p>
                        <p>Student Name | Guardian Name | Phone Number</p>
                        <p class="mt-2">Example:</p>
                        <p class="italic">John Doe | Jane Doe | 08012345678</p>
                    </div>

                    <!-- Bulk Input -->
                    <div>
                        <label class="block mb-1">Paste Student List</label>
                        <textarea name="bulk_data" rows="8"
                                  class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  placeholder="Paste WhatsApp list here..."
                                  required>{{ old('bulk_data') }}</textarea>

                        @error('bulk_data')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Class Selection -->
                    <div>
                        <label class="block mb-1">Class</label>
                        <select name="class_id"
                                class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            Import Students
                        </button>

                        <a href="{{ route('students.index') }}"
                           class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                            Back
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
