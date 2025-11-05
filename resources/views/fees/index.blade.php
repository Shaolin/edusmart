<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
          
            <h2 class="font-semibold text-xl dark:bg-gray-900 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 transition-colors duration-300 sm:w-auto text-center sm:text-left">
                Fee Management
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('fees.create') }}" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">
                    + Add Fee
                </a>

              
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-lg p-4 sm:p-6 font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300">

            <!-- ✅ TABLE VIEW (Visible on md and above) -->
            <div class="overflow-x-auto rounded-lg hidden md:block">
                <table class="w-full border-collapse min-w-[700px]">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">#</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Class</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Fee Name</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Amount</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Term</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Session</th>
                            <th class="border px-4 py-2 text-left text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($fees as $fee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2">{{ $fee->schoolClass->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $fee->name }}</td>
                                <td class="border px-4 py-2">₦{{ number_format($fee->amount) }}</td>
                                <td class="border px-4 py-2 capitalize">{{ $fee->term }}</td>
                                <td class="border px-4 py-2">{{ $fee->session }}</td>
                                <td class="border px-4 py-2 space-x-3 whitespace-nowrap">

                                    <a href="{{ route('fees.edit', $fee) }}" 
                                        class="text-blue-600 dark:text-blue-400 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('fees.destroy', $fee) }}" 
                                            method="POST" 
                                            class="inline"
                                            onsubmit="return confirm('Delete this fee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="text-red-600 dark:text-red-400 hover:underline">
                                            Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No fees defined yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 📱 MOBILE CARD VIEW (Visible below md) -->
            <div class="space-y-4 md:hidden">
                @forelse($fees as $fee)
                    <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 shadow-sm">

                        <div class="mb-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Fee Name</span>
                            <p class="text-base font-semibold">{{ $fee->name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Class</span>
                                <p class="font-medium">{{ $fee->schoolClass->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Amount</span>
                                <p class="font-medium">₦{{ number_format($fee->amount) }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Term</span>
                                <p class="font-medium capitalize">{{ $fee->term }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Session</span>
                                <p class="font-medium">{{ $fee->session }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-4 text-sm">
                            <a href="{{ route('fees.edit', $fee) }}" 
                                class="text-blue-600 dark:text-blue-400 font-medium">
                                Edit
                            </a>

                            <form action="{{ route('fees.destroy', $fee) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this fee?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No fees defined yet.
                    </p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $fees->links() }}
            </div>

        </div>
    </div>

    <!-- Dark Mode Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-dark');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('dark-mode') === 'true') htmlEl.classList.add('dark');

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    </script>
</x-app-layout>
