<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'SmartEdu') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col justify-center items-center px-4 transition-colors duration-300">

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
                rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">

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

        <!-- Slot for page content (login/register form) -->
        {{ $slot }}

    </div>

    <!-- Home Link -->
    <a href="{{ url('/') }}" class="mt-6 text-sm text-gray-400 hover:text-gray-200 transition">
        ← Back to Home
    </a>

    <!-- 🌙 Dark Mode Toggle -->
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 p-3 rounded-full bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 shadow-md transition">
        🌙
    </button>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // Load saved theme
        if(localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            if(html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>
</html>
