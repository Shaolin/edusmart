<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        {{-- Local Development: Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @elseif (file_exists($manifestPath))
        {{-- Production Build: use compiled assets --}}
        @php
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $appCss = $manifest['resources/css/app.css']['file'] ?? null;
            $appJs = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp

        @if ($appCss)
            <link rel="stylesheet" href="{{ asset('build/' . $appCss) }}">
        @endif
        @if ($appJs)
            <script src="{{ asset('build/' . $appJs) }}" defer></script>
        @endif
    @else
        {{-- Fallback (prevents blank page if build missing) --}}
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    @endif

</head>
<body class="font-sans antialiased bg-gray-900 text-white">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="bg-blue-600 text-white text-3xl font-bold px-8 py-3 rounded-2xl shadow-lg hover:bg-blue-700 transition">
                    SmartEdu
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-gray-800 shadow-lg overflow-hidden sm:rounded-2xl border border-gray-700">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
