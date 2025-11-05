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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- @include('layouts.navigation') --}}

          <!-- Page Heading -->
          @if (isset($header))
          <header class="bg-white shadow">
              <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                  {{ $header }}
              </div>
          </header>
          @endif
          
          <!-- Main content -->
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
              {{ $slot }}
          </div>
          
          <!-- Back to Dashboard Button at the bottom -->
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center">
              <a href="{{ route('dashboard') }}"
                 class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 text-sm sm:text-base">
                  &larr; Back to Dashboard
              </a>
          </div>
          

           
<script>
    const htmlEl = document.documentElement;
    const toggleBtns = document.querySelectorAll('#toggle-dark');

    // Apply saved preference
    if (localStorage.getItem('dark-mode') === 'true') {
        htmlEl.classList.add('dark');
    }

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('dark-mode', htmlEl.classList.contains('dark'));
        });
    });
</script>

        
    </body>
</html>
