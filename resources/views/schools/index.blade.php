<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
           
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
              School Information
             </h2>
           
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 transition duration-300">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 p-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- No School Message --}}
            @if(!$school)
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-300 text-sm">No school information available yet.</p>
                    <a href="{{ route('schools.create') }}"
                        class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Create School
                    </a>
                </div>
            @else
                {{-- School Profile --}}
                <div class="flex flex-col sm:flex-row sm:items-start gap-4 mb-6">

                    {{-- Logo --}}
                    <div class="flex-shrink-0 flex justify-center sm:block">
                        @if($school->logo)
                            <img src="{{ asset('storage/' . $school->logo) }}" 
                                alt="School Logo" 
                                class="h-24 w-24 object-contain rounded shadow">
                               
                      
     {{-- <img src="{{ route('school.logo', ['filename' => $school->logo]) }}"
     class="h-24 w-24 object-contain rounded shadow"
     alt="School Logo"> --}}


                           

                        @else
                            <div class="h-24 w-24 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                                <span class="text-gray-500 dark:text-gray-300 text-xs">No Logo</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="space-y-1 text-sm sm:text-base">
                        <h3 class="text-xl font-semibold">{{ $school->name ?? 'No School Name Set' }}</h3>

                        <p><span class="font-semibold">Address:</span> {{ $school->address ?? 'Not set' }}</p>
                        <p><span class="font-semibold">Contact Info:</span> {{ $school->phone ?? 'Not set' }}</p>

                        @if(!empty($school->email))
                            <p><span class="font-semibold">Email:</span> {{ $school->email }}</p>
                        @endif

                        @if(!empty($school->website))
                            <p>
                                <span class="font-semibold">Website:</span> 
                                <a href="{{ $school->website }}" target="_blank" class="text-blue-500 hover:underline break-all">
                                    {{ $school->website }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('schools.edit', $school->id) }}" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                        Edit
                    </a>

                    <form action="{{ route('schools.destroy', $school->id) }}" method="POST" 
                        onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            Delete
                        </button>
                    </form>

                    <a href="{{ route('dashboard') }}" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                        Back to Dashboard
                    </a>
                </div>

            @endif
        </div>
    </div>

    {{-- Dark Mode Toggle --}}
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
