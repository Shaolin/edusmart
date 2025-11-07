<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class', // enable class-based dark mode
        theme: {
            extend: {},
        }
    }
</script>


    <!-- Optional custom CSS -->
    <style>
        .fade-in {
            animation: fadeIn 1.2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile menu animation */
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">

    <div class="min-h-screen flex flex-col">

        {{-- Page Heading --}}
        @if (isset($header))
            <header class="bg-white shadow-sm border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 max-w-7xl mx-auto w-full py-8 px-4 sm:px-6 lg:px-8 pb-32">
            {{ $slot }}
        </main>
    </div>

    {{-- 🔙 Back to Dashboard --}}
    @auth
        <a href="{{ route('dashboard') }}"
           class="fixed bottom-20 left-6 inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            ⬅️ Back to Dashboard
        </a>
    @endauth

    {{-- 🏠 Home Button --}}
    <a href="{{ url('/') }}"
       class="fixed bottom-6 left-6 inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-gray-700 rounded-full shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
        🏠 Home
    </a>

    {{-- 🌙 Dark Mode Toggle --}}
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 p-3 rounded-full bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 shadow-md transition">
        🌙
    </button>

    {{-- Mobile menu toggle JS --}}
    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById("theme-toggle");

        if (localStorage.getItem("theme") === "dark") {
            html.classList.add("dark");
        }

        themeToggle.addEventListener("click", () => {
            html.classList.toggle("dark");
            localStorage.setItem("theme", html.classList.contains("dark") ? "dark" : "light");
        });

        // Mobile menu open/close
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenu = document.getElementById('close-menu');

        if(menuBtn && mobileMenu && closeMenu){
            menuBtn.addEventListener('click', () => mobileMenu.classList.add('open'));
            closeMenu.addEventListener('click', () => mobileMenu.classList.remove('open'));
        }
    </script>
</body>
</html>
