
<!-- guest.blade file -->


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

    @php
        $isLocal = app()->environment('local');
        $manifestPath = public_path('build/manifest.json');
    @endphp

    @if ($isLocal)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @elseif (file_exists($manifestPath))
        @php
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $appCss = $manifest['resources/css/app.css']['file'] ?? null;
            $appJs = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp

        @if ($appCss)
            <link rel="stylesheet" href="{{ asset('build/'.$appCss) }}">
        @endif
        @if ($appJs)
            <script src="{{ asset('build/'.$appJs) }}" defer></script>
        @endif
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    @endif
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
        <div class="w-full sm:max-w-md px-6 py-8  shadow-xl sm:rounded-2xl border border-gray-200 text-gray-900">
            <style>
                label {
                    color: #2563eb; /* Tailwind blue-600 */
                    font-weight: 500;
                }
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
