<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pricing | SmartEdu</title>
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
    .glass {
      background: rgba(17, 25, 40, 0.7);
      backdrop-filter: blur(10px);
    }
    /* Slide-in mobile menu */
    .mobile-menu {
      position: fixed;
      top: 0;
      left: -100%;
      height: 100%;
      width: 70%;
      max-width: 260px;
      background-color: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(10px);
      border-right: 1px solid #1f2937;
      transition: left 0.3s ease-in-out;
      z-index: 50;
      padding: 2rem 1.5rem;
    }
    .mobile-menu.active {
      left: 0;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="bg-gray-900 bg-opacity-70 backdrop-blur-md border-b border-gray-800 px-6 py-4 flex justify-between items-center relative z-40">
    <a href="/" class="text-blue-400 text-2xl font-bold">SmartEdu</a>

    <!-- Desktop Menu -->
    <div class="hidden md:flex space-x-6 text-gray-300">
      <a href="/" class="hover:text-blue-400">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="text-blue-400 font-semibold">Pricing</a>
      {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a> --}}
    </div>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
      <i data-lucide="menu" class="w-7 h-7"></i>
    </button>
  </nav>

  <!-- Slide-in Mobile Menu -->
  <div id="mobileMenu" class="mobile-menu">
    <button id="closeMenu" class="text-gray-400 mb-6 focus:outline-none">
      <i data-lucide="x" class="w-6 h-6"></i>
    </button>

    <nav class="flex flex-col space-y-4 text-gray-300">
      <a href="/" class="hover:text-blue-400">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="hover:text-blue-400">Contact</a>
      <a href="/pricing" class="text-blue-400 font-semibold">Pricing</a>
      {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a> --}}
    </nav>
  </div>

  <!-- Hero / Banner -->
  <section class="text-center py-16 fade-up px-6">
    <h1 class="text-4xl md:text-5xl font-bold text-blue-400 mb-4">Simple & Transparent Pricing</h1>
    <p class="text-gray-400 max-w-2xl mx-auto">
      Choose the perfect plan for your school. Start free, then upgrade when ready.
    </p>

    <div class="mt-6 bg-blue-900 bg-opacity-40 text-blue-300 inline-block px-6 py-3 rounded-full text-sm font-medium border border-blue-700">
      🎓 Try SmartEdu Free for One Term — No payment required!
    </div>
  </section>

  <!-- Pricing Plans -->
  <section class="grid grid-cols-1 md:grid-cols-2 gap-8 px-6 md:px-16 pb-16 fade-up">
    <!-- Standard Plan -->
    <div class="glass p-8 rounded-2xl border border-gray-800 hover:border-blue-500 transition duration-300 text-center">
      <h3 class="text-2xl font-semibold text-blue-400 mb-2">Standard</h3>
      <p class="text-gray-400 mb-6">₦15,000 / term</p>
      <ul class="text-gray-300 text-sm space-y-3 mb-8">
        <li>✅ Manage students, fees & results</li>
        <li>✅ WhatsApp results & receipts</li>
        <li>✅ School dashboard & analytics</li>
        <li>✅ Secure data storage</li>
      </ul>
      <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-medium">
        Choose Standard
      </a>
    </div>

    <!-- Premium Plan -->
    <div class="glass p-8 rounded-2xl border border-blue-500 hover:border-blue-400 transition duration-300 text-center transform md:-translate-y-4 shadow-lg shadow-blue-900/40">
      <h3 class="text-2xl font-semibold text-blue-400 mb-2">Premium</h3>
      <p class="text-gray-400 mb-6">₦25,000 / term</p>
      <ul class="text-gray-300 text-sm space-y-3 mb-8">
        <li>✨ Everything in Standard</li>
        <li>✨ Broadcasts & announcements</li>
        <li>✨ Bulk parent reminders</li>
        <li>✨ Dedicated support</li>
      </ul>
      <a href="/register" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-medium">
        Choose Premium
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="mt-auto text-center text-gray-500 py-6 border-t border-gray-800">
    © {{ date('Y') }} SmartEdu — Powered by Sawo Software Systems
  </footer>


  <script>
    lucide.createIcons();

    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.getElementById('closeMenu');

    menuBtn.addEventListener('click', () => {
      mobileMenu.classList.add('active');
    });

    closeMenu.addEventListener('click', () => {
      mobileMenu.classList.remove('active');
    });
  </script>
</body>
</html>
