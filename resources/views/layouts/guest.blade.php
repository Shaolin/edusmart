<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'SmartEdu') }}</title>

    <!-- Tailwind CDN - no build step required -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: configure Tailwind CDN for dark mode if you use it -->
    <script>
      // you can enable dark mode using class strategy if you prefer:
      tailwind.config = { darkMode: 'class' }
    </script>

    <!-- If you still have a custom app.css/js in public, keep them as optional fallbacks -->
    @if (file_exists(public_path('css/app.css')))
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    @if (file_exists(public_path('js/app.js')))
        <script src="{{ asset('js/app.js') }}" defer></script>
    @endif

    <style>
      /* small safety: ensure auth card is visible even if dark class behaves differently */
      body { background-color: #111827; color: #f9fafb; }
    </style>
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
        <div class="w-full sm:max-w-md px-6 py-8 text-gray-900 shadow-xl sm:rounded-2xl border border-gray-200">
            <style>
                /* label accent for accessibility */
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
