<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SmartEdu') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- ✅ Direct CSS & JS for Shared Hosting (NO VITE) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased bg-gray-900 text-gray-100">

    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        
        <!-- Logo / Title -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="bg-blue-600 text-white text-3xl font-bold px-8 py-3 rounded-2xl shadow-md hover:bg-blue-700 transition">
                    SmartEdu
                </div>
            </a>
        </div>

        <!-- Auth Card -->
        <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100">
            <style>
                label { color: #2563eb; font-weight: 500; }
            </style>
        
            {{ $slot }}
        </div>

        <!-- Home Link -->
        <a href="{{ url('/') }}" class="mt-6 text-sm text-gray-400 hover:text-gray-200 transition">
            ← Back to Home
        </a>
    </div>

</body>
</html>
