<x-app-layout>
    <x-slot name="header">
       
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
              Add New Fee
            </h2>

          
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <form action="{{ route('fees.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Grid: Class & Fee Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Class -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Class</label>
                        <select name="class_id" 
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Fee Name -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Fee Name</label>
                        <input type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Grid: Amount & Term -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Amount (₦)</label>
                        <input type="number" 
                            name="amount" 
                            step="0.01" 
                            value="{{ old('amount') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                        @error('amount')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Term -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Term</label>
                        <select name="term" 
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">-- Select Term --</option>
                            <option value="first" {{ old('term') == 'first' ? 'selected' : '' }}>First Term</option>
                            <option value="second" {{ old('term') == 'second' ? 'selected' : '' }}>Second Term</option>
                            <option value="third" {{ old('term') == 'third' ? 'selected' : '' }}>Third Term</option>
                        </select>
                        @error('term')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Session -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1">Session</label>
                    <input type="text" 
                        name="session" 
                        placeholder="e.g. 2024/2025"
                        value="{{ old('session') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                    @error('session')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                    <a href="{{ route('fees.index') }}" 
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Fee
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
