<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="name" class="block font-semibold mb-2">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                          dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="email" class="block font-semibold mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                          dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="password" class="block font-semibold mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                          dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block font-semibold mb-2">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                          dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
        </div>

        @if(\App\Models\User::count() === 0)
            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">School Details</h3>

            <div class="mb-4">
                <label for="school_name" class="block font-semibold mb-2">School Name</label>
                <input type="text" id="school_name" name="school_name" value="{{ old('school_name') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                              dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="school_logo" class="block font-semibold mb-2">Logo (optional)</label>
                <input type="file" id="school_logo" name="school_logo"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-base
                              dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="school_phone" class="block font-semibold mb-2">Phone (optional)</label>
                <input type="text" id="school_phone" name="school_phone" value="{{ old('school_phone') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                              dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="school_address" class="block font-semibold mb-2">Address (optional)</label>
                <input type="text" id="school_address" name="school_address" value="{{ old('school_address') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                              dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="school_website" class="block font-semibold mb-2">Website (optional)</label>
                <input type="text" id="school_website" name="school_website" value="{{ old('school_website') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-base
                              dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            </div>
        @endif

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md text-base transition-all">
            Register
        </button>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Already registered?
            <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline transition">
                Log in
            </a>
        </p>
    </form>
</x-guest-layout>
