<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'SmartEdu') }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>

    <style>
        body { background-color: #111827; color: #f9fafb; }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex flex-col justify-center items-center px-4">

    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-block">
            <div class="bg-blue-600 text-white text-3xl font-bold px-8 py-3 rounded-2xl shadow-md hover:bg-blue-700 transition">
                SmartEdu
            </div>
        </a>
    </div>

    <!-- Auth Card -->
    <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">

        {{-- ✅ Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300 text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Auth Form Slot -->
        {{ $slot }}

        {{-- Optional: Forgot Password --}}
        @if (Route::has('password.request'))
            <div class="mt-4 text-right">
                <a href="{{ route('password.request') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline transition">
                    Forgot your password?
                </a>
            </div>
        @endif

    </div>

    <!-- Home Link -->
    <a href="{{ url('/') }}" class="mt-6 text-sm text-gray-400 hover:text-gray-200 transition">
        ← Back to Home
    </a>

</body>
</html>
