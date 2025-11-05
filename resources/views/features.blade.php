<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Features | SmartEdu</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body {
      background: linear-gradient(135deg, #0a0f1c, #0b1730);
      color: #e4e7eb;
      font-family: 'Inter', sans-serif;
    }
    .fade-up {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.8s ease-out forwards;
    }
    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
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
      <a href="/features" class="text-blue-400 font-semibold">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>
   
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
        <a href="/features" class="text-blue-400 font-semibold">Features</a>
        <a href="/about" class="hover:text-blue-400">About</a>
        <a href="/contact" class="hover:text-blue-400">Contact</a>
        <a href="/pricing" class="hover:text-blue-400">Pricing</a>
        {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a> --}}
      </nav>
    </div>
  </nav>

  <!-- Hero -->
  <section class="text-center py-16 fade-up px-6">
    <h1 class="text-4xl md:text-5xl font-bold text-blue-400 mb-4">Our Features</h1>
    <p class="text-gray-400 max-w-2xl mx-auto">
      Everything your school needs — all in one smart platform.
    </p>
  </section>

  <!-- Features Grid -->
  <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-8 md:px-16 pb-16">
    <!-- 1 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="file-text" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Student Management</h3>
      <p class="text-gray-400">Easily manage student records, admissions, and profiles with just a few clicks.</p>
    </div>

    <!-- 2 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="credit-card" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Fee Tracking</h3>
      <p class="text-gray-400">Keep track of fee payments, send reminders, and generate receipts instantly.</p>
    </div>

    <!-- 3 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="bar-chart" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Result Management</h3>
      <p class="text-gray-400">Generate and share results securely with students and parents on WhatsApp.</p>
    </div>

    <!-- 4 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="message-circle" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Smart Communication</h3>
      <p class="text-gray-400">Send automatic WhatsApp updates — receipts, results, and announcements.</p>
    </div>

    <!-- 5 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="wallet" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Wallet System</h3>
      <p class="text-gray-400">Schools top up their wallet to send WhatsApp messages.</p>
    </div>

    <!-- 6 -->
    <div class="bg-gray-900 bg-opacity-60 p-6 rounded-2xl hover:bg-gray-800 transition fade-up">
      <div class="text-blue-400 mb-4">
        <i data-lucide="shield" class="w-8 h-8"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Secure & Reliable</h3>
      <p class="text-gray-400">Data is safely encrypted and stored — ensuring privacy for every student and school.</p>
    </div>
  </section>

  <!-- CTA -->
  <section class="text-center py-16 fade-up">
    <h3 class="text-xl font-semibold mb-4 text-gray-200">Your school deserves the SmartEdu advantage.</h3>
    <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition">
      Get Started Now
    </a>
  </section>

  <!-- Footer -->
  <footer class="mt-auto text-center text-gray-500 py-6 border-t border-gray-800">
    © {{ date('Y') }} SmartEdu — Powered by Sawo Software Systems
  </footer>

  <script>
    lucide.createIcons();

    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenu = document.getElementById('close-menu');

    menuBtn.addEventListener('click', () => mobileMenu.classList.add('open'));
    closeMenu.addEventListener('click', () => mobileMenu.classList.remove('open'));
  </script>

</body>
</html>
