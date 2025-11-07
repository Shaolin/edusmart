<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | EduSmart</title>
  <!-- Load Tailwind via CDN like your working pages -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0a0f1c, #0b1730);
      color: #e4e7eb;
      font-family: 'Inter', sans-serif;
    }
    .fade-in {
      animation: fadeIn 1s ease-out forwards;
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
<body class="min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="bg-gray-900 bg-opacity-70 backdrop-blur-md border-b border-gray-800 px-6 py-4 flex justify-between items-center relative">
    <a href="/" class="text-blue-400 text-2xl font-bold">EduSmart</a>

    <!-- Desktop Menu -->
    <div class="hidden md:flex space-x-6 text-gray-300 items-center">
      <a href="/" class="text-blue-400 font-semibold">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>

      @auth
        <a href="{{ url('/dashboard') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg ml-2">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a>
        <a href="{{ route('register') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg">Register</a>
      @endauth
    </div>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 left-0 w-64 h-full bg-gray-900 bg-opacity-95 backdrop-blur-md border-r border-gray-800 p-6 z-50">
      <button id="close-menu" class="text-gray-400 hover:text-white mb-8 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col space-y-4 text-gray-300">
        <a href="/" class="text-blue-400 font-semibold">Home</a>
        <a href="/features" class="hover:text-blue-400">Features</a>
        <a href="/about" class="hover:text-blue-400">About</a>
        <a href="/contact" class="hover:text-blue-400">Contact</a>
        <a href="/pricing" class="hover:text-blue-400">Pricing</a>

        @auth
          <a href="{{ url('/dashboard') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg text-center">Dashboard</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg w-full mt-2">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a>
          <a href="{{ route('register') }}" class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg text-center">Register</a>
        @endauth
      </nav>
    </div>
  </nav>

  <!-- Hero Section -->
  <main class="flex-grow flex items-center justify-center px-6 fade-in">
    <div class="text-center space-y-6">
      <div class="flex justify-center mb-4">
        <div class="bg-blue-600 text-white font-bold text-3xl px-6 py-2 rounded-2xl shadow-lg">
          EduSmart
        </div>
      </div>
      <h1 class="text-3xl md:text-4xl font-semibold">
        Simplify School Management. <br>
        <span class="text-blue-400">Fast. Smart. Connected.</span>
      </h1>
      <p class="text-gray-400 text-base md:text-lg max-w-lg mx-auto">
        EduSmart helps schools manage students, results, and payments effortlessly — all in one place.
      </p>

      <div class="flex flex-col md:flex-row justify-center gap-4 mt-8">
        @auth
          <a href="{{ url('/dashboard') }}"
             class="bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-3 px-8 rounded-full">
             Go to Dashboard
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="border border-red-600 text-red-400 hover:bg-red-600 hover:text-white transition font-semibold py-3 px-8 rounded-full">
              Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-3 px-8 rounded-full">
             Login
          </a>
          <a href="{{ route('register') }}"
             class="border border-blue-600 text-blue-400 hover:bg-blue-600 hover:text-white transition font-semibold py-3 px-8 rounded-full">
             Register
          </a>
        @endauth
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-gray-500 text-sm text-center py-6 border-t border-gray-800">
    &copy; {{ date('Y') }} EduSmart · Powered by Sawo Software Systems
  </footer>

  <script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenu = document.getElementById('close-menu');

    menuBtn.addEventListener('click', () => mobileMenu.classList.add('open'));
    closeMenu.addEventListener('click', () => mobileMenu.classList.remove('open'));
  </script>

</body>
</html>
