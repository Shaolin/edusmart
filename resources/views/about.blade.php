<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | SmartEdu</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0a0f1c, #0b1730);
      color: #e4e7eb;
      font-family: 'Inter', sans-serif;
    }
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
<body class="min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="bg-gray-900 bg-opacity-70 backdrop-blur-md border-b border-gray-800 px-6 py-4 flex justify-between items-center relative">
    <a href="/" class="text-blue-400 text-2xl font-bold">SmartEdu</a>

    <!-- Desktop Menu -->
    <div class="hidden md:flex space-x-6 text-gray-300">
      <a href="/" class="hover:text-blue-400">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="text-blue-400 font-semibold">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>
      {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a> --}}
    </div>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
      <!-- hamburger -->
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 left-0 w-64 h-full bg-gray-900 bg-opacity-95 backdrop-blur-md border-r border-gray-800 p-6 z-50">
      <button id="close-menu" class="text-gray-400 hover:text-white mb-8 focus:outline-none">
        <!-- X icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <nav class="flex flex-col space-y-4 text-gray-300">
        <a href="/" class="hover:text-blue-400">Home</a>
        <a href="/features" class="hover:text-blue-400">Features</a>
        <a href="/about" class="text-blue-400 font-semibold">About</a>
        <a href="/contact" class="hover:text-blue-400">Contact</a>
        <a href="/pricing" class="hover:text-blue-400">Pricing</a>
        {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a> --}}
      </nav>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="flex flex-col items-center text-center py-16 fade-in px-6">
    <h1 class="text-4xl md:text-5xl font-bold text-blue-400 mb-4">About SmartEdu</h1>
    <p class="text-gray-400 max-w-2xl">
      Empowering schools with technology — SmartEdu by Sawo Technologies helps schools simplify fees, results, and communication all in one system.
    </p>
  </section>

  <!-- Who We Are -->
  <section class="max-w-4xl mx-auto px-6 md:px-12 py-10 fade-in">
    <h2 class="text-2xl font-semibold text-blue-300 mb-4">Who We Are</h2>
    <p class="text-gray-300 leading-relaxed">
      SmartEdu is an innovation by <span class="text-blue-400 font-semibold">Sawo Technologies</span>, created to help schools move away from manual record keeping and adopt modern digital tools.
      Our goal is to make administration simple, efficient, and affordable for schools of all sizes.
    </p>
  </section>

  <!-- Mission & Vision -->
  <section class="bg-gray-900 bg-opacity-50 py-10 px-6 md:px-12 fade-in">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-2xl font-semibold text-blue-300 mb-4">Our Mission & Vision</h2>
      <p class="text-gray-300 leading-relaxed mb-4">
        Our mission is to make school management easier and communication faster through technology that’s reliable and affordable.
      </p>
      <p class="text-gray-300 leading-relaxed">
        Our vision is to become Africa’s most trusted provider of smart school technology, helping institutions grow and improve learning outcomes.
      </p>
    </div>
  </section>

  <!-- CTA -->
  <section class="text-center py-16 fade-in">
    <h3 class="text-xl font-semibold mb-4 text-gray-200">Ready to modernize your school?</h3>
    <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition">
      Get Started
    </a>
  </section>

  <!-- Footer -->
  <footer class="mt-auto text-center text-gray-500 py-6 border-t border-gray-800">
    © {{ date('Y') }} SmartEdu — Powered by Sawo Software Systems
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
