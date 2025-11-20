<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl sm:text-3xl rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                Assign Subject to Class
            </h2>
        </div>
    </x-slot>
    @if($errors->any())
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="max-w-3xl mx-auto mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
         <form action="{{ route('class_subject_teacher.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Select Class --}}
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Select Class
                </label>
                <select id="class_id" name="class_id" required
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100
                               focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                    <option value="">-- Choose Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Select Subject --}}
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Select Subject
                </label>
                <select id="subject_id" name="subject_id" required
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100
                               focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                    <option value="">-- Choose Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->level }})</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Select Teacher --}}
            <div>
                <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Select Teacher
                </label>
                <select id="teacher_id" name="teacher_id" required
                        class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-100
                               focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                    <option value="">-- Choose Teacher --</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                @endforeach
                
                </select>
                @error('teacher_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('class_subject_teacher.index') }}"
                   class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">
                    Assign Subject
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
