<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 dark:bg-gray-900 rounded-lg px-4 py-2 w-full sm:w-auto text-center transition-colors duration-300">
                Edit Student
            </h2>

          
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-3 sm:px-6 lg:px-8">
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <form action="{{ route('students.update', $student->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Student Name -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Student Name</label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Admission Number -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Admission Number</label>
                        <input type="text" name="admission_number" value="{{ old('admission_number', $student->admission_number) }}"
                            class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                        @error('admission_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Gender</label>
                        <select name="gender"
                            class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $student->gender)=='male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender)=='female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Class</label>
                        <select name="class_id"
                            class="w-full px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $student->class_id)==$class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Guardian -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200 mb-1">Guardian</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <select name="guardian_id" id="guardian-select"
                                class="flex-1 px-3 py-2 border rounded-lg text-sm sm:text-base dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select Guardian</option>
                                @foreach($guardians as $guardian)
                                    <option value="{{ $guardian->id }}" {{ old('guardian_id', $student->guardian_id)==$guardian->id ? 'selected' : '' }}>
                                        {{ $guardian->name }} ({{ $guardian->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="add-guardian-btn"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none transition w-full sm:w-auto">
                                Add New
                            </button>
                        </div>
                        @error('guardian_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Guardian Modal -->
    <div id="guardian-modal"
        class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50 px-4 sm:px-0">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md mx-auto">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 text-center">Add New Guardian</h3>
            <form id="guardian-form" class="space-y-3">
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1">Name</label>
                    <input type="text" id="guardian-name"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1">Phone</label>
                    <input type="text" id="guardian-phone"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 mb-1">Email</label>
                    <input type="email" id="guardian-email"
                        class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                    <button type="button" id="guardian-cancel"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded-lg w-full sm:w-auto">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none w-full sm:w-auto">
                        Add
                    </button>
                </div>
            </form>
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

        // Guardian Modal
        const modal = document.getElementById('guardian-modal');
        const addBtn = document.getElementById('add-guardian-btn');
        const cancelBtn = document.getElementById('guardian-cancel');
        const guardianForm = document.getElementById('guardian-form');
        const guardianSelect = document.getElementById('guardian-select');

        addBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

        guardianForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('guardian-name').value;
            const phone = document.getElementById('guardian-phone').value;
            const email = document.getElementById('guardian-email').value;

            const newOption = document.createElement('option');
            newOption.value = 'new';
            newOption.text = `${name} (${phone})`;
            newOption.selected = true;
            guardianSelect.appendChild(newOption);

            modal.classList.add('hidden');
            guardianForm.reset();
        });
    </script>
</x-app-layout>
