<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            {{-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight transition-colors 
            duration-300"> --}}
            <h2 class="font-semibold text-xl dark:bg-gray-900  rounded-lg px-4 py-2  text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center">

                Students
            </h2>

            <div class="flex flex-wrap gap-2 items-center">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('students.create') }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors duration-300 text-sm sm:text-base w-full sm:w-auto text-center">
                        Add Student
                    </a>
                @endif

               
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 text-gray-900 dark:text-gray-100 transition-colors duration-300">

                <!-- FILTER ROW -->
                <form method="GET" action="{{ route('students.index') }}" class="mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        <div class="sm:col-span-2 md:col-span-2 lg:col-span-3">
                            <label for="name" class="sr-only">Name</label>
                            <input id="name" name="name" value="{{ request('name') }}"
                                placeholder="Search by name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 dark:text-gray-100 text-sm sm:text-base transition-colors duration-300"
                            />
                        </div>

                        <div>
                            <label for="class_id" class="sr-only">Class</label>
                            <select id="class_id" name="class_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 dark:text-gray-100 text-sm sm:text-base transition-colors duration-300">
                                <option value="">-- All Classes --</option>
                                @foreach($classes ?? [] as $cls)
                                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }} {{ $cls->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="gender" class="sr-only">Gender</label>
                            <select id="gender" name="gender"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 dark:text-gray-100 text-sm sm:text-base transition-colors duration-300">
                                <option value="">-- All Genders --</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 text-sm sm:text-base">
                                Filter
                            </button>
                            <a href="{{ route('students.index') }}"
                               class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg transition-colors duration-300 text-sm sm:text-base text-center">
                               Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- DESKTOP TABLE -->
                <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Admission No</th>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Gender</th>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Class</th>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Guardian</th>
                                <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <td class="px-4 sm:px-6 py-3">{{ $student->name }}</td>
                                    <td class="px-4 sm:px-6 py-3">{{ $student->admission_number }}</td>
                                    <td class="px-4 sm:px-6 py-3">{{ ucfirst($student->gender) }}</td>
                                    <td class="px-4 sm:px-6 py-3">{{ $student->schoolclass->name ?? '-' }}</td>
                                    <td class="px-4 sm:px-6 py-3">{{ $student->guardian->name ?? '-' }}</td>
                                    <td class="px-4 sm:px-6 py-3 space-x-2">
                                        <a href="{{ route('students.show', $student->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                        <a href="{{ route('students.edit', $student->id) }}" class="text-yellow-600 dark:text-yellow-400 hover:underline">Edit</a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="space-y-3 md:hidden">
                    @foreach($students as $student)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800 shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $student->name }}</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($student->gender) }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Admission No:</strong> {{ $student->admission_number }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Class:</strong> {{ $student->schoolclass->name ?? '-' }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Guardian:</strong> {{ $student->guardian->name ?? '-' }}</p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('students.show', $student->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">View</a>
                                <a href="{{ route('students.edit', $student->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded-md text-xs hover:bg-yellow-600">Edit</a>
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $students->links() }}
                </div>
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
