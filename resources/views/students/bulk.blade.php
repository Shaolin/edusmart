<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Bulk Import Students
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

            <form action="{{ route('students.bulkStore') }}" method="POST">
                @csrf

                <div>
                    <label class="block mb-2">Paste Student List</label>
                    <textarea name="students"
                              rows="10"
                              class="w-full border rounded p-3"
                              placeholder="John Doe, Jane Doe, 08012345678"></textarea>
                </div>

                <button class="mt-4 px-6 py-2 bg-blue-600 text-white rounded">
                    Import Students
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
