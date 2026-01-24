<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                Class Attendance
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Tap a student to mark <span class="font-semibold">ABSENT</span>
            </p>
        </div>
    </x-slot>

    {{-- Date selector --}}
<form method="GET" class="mb-4 px-3">
    <label
        class="block mb-1 text-sm font-semibold
               text-gray-700 dark:text-gray-200">
        Select date
    </label>

    <input
        type="date"
        name="date"
        value="{{ $date }}"
        class="px-3 py-2 border rounded-lg w-full sm:w-auto
               bg-white text-gray-900
               dark:bg-gray-800 dark:text-gray-100
               border-gray-300 dark:border-gray-600
               focus:ring-2 focus:ring-blue-500 focus:outline-none"
        onchange="this.form.submit()"
    >
</form>


    <div class="py-4">
        <div class="max-w-3xl mx-auto px-3">

            {{-- success message --}}
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('teachers.attendance.store') }}">
                @csrf

                <p class="mb-3 text-sm text-gray-600">
                    Students count: {{ $students->count() }}
                </p>

                <div class="space-y-3">
                    @foreach($students as $student)
                        @php
                            $record = $attendance[$student->id] ?? null;
                            $isAbsent = $record && $record->status === 'absent';
                        @endphp

                        {{-- Hidden input (default state) --}}
                        <input
                            type="hidden"
                            name="attendance[{{ $student->id }}]"
                            value="{{ $isAbsent ? 'absent' : 'present' }}"
                        >

                        <button
                            type="button"
                            onclick="toggleAttendance(this)"
                            class="attendance-btn w-full flex justify-between items-center
                                   p-4 rounded-lg border
                                   {{ $isAbsent ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}
                                   active:scale-95 transition">

                            <div class="text-left">
                                <p class="font-medium">
                                    {{ $student->name }}
                                </p>
                                <p class="text-xs opacity-70">
                                    {{ $student->admission_number }}
                                </p>
                            </div>

                            <span class="status font-semibold">
                                {{ $isAbsent ? 'Absent' : 'Present' }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <button
                    type="submit"
                    class="w-full mt-6 py-3 rounded-lg
                           bg-blue-600 text-white text-lg
                           hover:bg-blue-700">
                    Save Attendance
                </button>
            </form>
        </div>
    </div>

    {{-- tiny JS --}}
    <script>
        function toggleAttendance(button) {
            const input = button.previousElementSibling;
            const status = button.querySelector('.status');

            if (!input) return;

            if (input.value === 'present') {
                input.value = 'absent';
                status.textContent = 'Absent';
                button.classList.remove('bg-green-100', 'text-green-800');
                button.classList.add('bg-red-100', 'text-red-800');
            } else {
                input.value = 'present';
                status.textContent = 'Present';
                button.classList.remove('bg-red-100', 'text-red-800');
                button.classList.add('bg-green-100', 'text-green-800');
            }
        }
    </script>
</x-app-layout>
