<x-app-layout>
    <x-slot name="header">
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
               Edit Fee - {{$fee->name}}
            </h2>

            
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

            <form action="{{ route('fees.update', $fee) }}" method="POST" class="space-y-5 sm:space-y-6">
                @csrf
                @method('PUT')

                <!-- Class -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Class</label>
                    <select name="class_id" 
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $fee->class_id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fee Name -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Fee Name</label>
                    <input type="text" 
                        name="name" 
                        value="{{ old('name', $fee->name) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Amount (₦)</label>
                    <input type="number" 
                        name="amount" 
                        step="0.01" 
                        value="{{ old('amount', $fee->amount) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <!-- Term + Session (side-by-side on md+, stacked on mobile) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Term -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Term</label>
                        <select name="term" 
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                            <option value="first" {{ $fee->term == 'first' ? 'selected' : '' }}>First Term</option>
                            <option value="second" {{ $fee->term == 'second' ? 'selected' : '' }}>Second Term</option>
                            <option value="third" {{ $fee->term == 'third' ? 'selected' : '' }}>Third Term</option>
                        </select>
                    </div>

                    <!-- Session -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Session</label>
                        <input type="text" 
                            name="session" 
                            value="{{ old('session', $fee->session) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                    <a href="{{ route('fees.index') }}" 
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Fee
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
