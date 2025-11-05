<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | SmartEdu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body {
      background: #0f172a;
      color: #e2e8f0;
      font-family: 'Inter', sans-serif;
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
    /* Overlay behind menu */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease;
      z-index: 40;
    }
    .overlay.active {
      opacity: 1;
      visibility: visible;
    }
  </style>
</head>
<body class="bg-[#0f172a] text-gray-200 font-sans">

  <!-- Navbar -->
  <nav class="bg-gray-900 bg-opacity-70 backdrop-blur-md border-b border-gray-800 px-6 py-4 flex justify-between items-center relative z-40">
    <a href="/" class="text-blue-400 text-2xl font-bold">SmartEdu</a>

    <!-- Desktop Links -->
    <div class="hidden md:flex space-x-6 text-gray-300">
      <a href="/" class="hover:text-blue-400">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="text-blue-400 font-semibold">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>
      {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a> --}}
    </div>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
      <i data-lucide="menu" class="w-7 h-7"></i>
    </button>
  </nav>

  <!-- Mobile Slide-in Menu -->
  <div id="mobileMenu" class="mobile-menu">
    <button id="closeMenu" class="text-gray-400 mb-6 focus:outline-none">
      <i data-lucide="x" class="w-6 h-6"></i>
    </button>

    <nav class="flex flex-col space-y-4 text-gray-300">
      <a href="/" class="hover:text-blue-400">Home</a>
      <a href="/features" class="hover:text-blue-400">Features</a>
      <a href="/about" class="hover:text-blue-400">About</a>
      <a href="/contact" class="text-blue-400 font-semibold">Contact</a>
      <a href="/pricing" class="hover:text-blue-400">Pricing</a>
      {{-- <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center">Login</a> --}}
    </nav>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="overlay"></div>

  <!-- 📞 Contact Section -->
  <section class="py-20 px-6">
    <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
      <!-- Left Info -->
      <div>
        <h2 class="text-4xl font-bold mb-6 text-blue-400">Get in Touch</h2>
        <p class="text-gray-400 mb-8">
          We’d love to hear from you! Whether you have a question about features, pricing, 
          or anything else, our team is always ready to assist.
        </p>

        <div class="space-y-5 text-gray-300">
          <div class="flex items-start space-x-3">
            <span class="text-blue-400 text-xl">📍</span>
            <p>No. 1 Amaijoka Street, Ugbene II, Enugu-East, Enugu, Nigeria</p>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-blue-400 text-xl">📞</span>
            <p>+234 7030920009</p>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-blue-400 text-xl">✉️</span>
            <p>support@smartedu.com.ng</p>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-blue-400 text-xl">💬</span>
            <p>
              Chat with us on WhatsApp:
              <a href="https://wa.me/2347030920009" class="text-blue-400 hover:underline">Click Here</a>
            </p>
          </div>
        </div>
      </div>

      <!-- Right Form -->
      <div class="bg-[#1e293b] p-8 rounded-2xl shadow-lg">
        <h3 class="text-2xl font-semibold mb-6 text-blue-300">Send a Message</h3>

        @if (session('success'))
          <div class="bg-green-600 text-white p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
          <div class="bg-red-600 text-white p-3 rounded mb-4">Please fill all fields correctly.</div>
        @endif

        <form action="#" method="POST" class="space-y-5">
          @csrf
          <div>
            <label class="block text-gray-400 mb-2">Your Name</label>
            <input type="text" name="name" placeholder="Enter your name"
                   class="w-full bg-[#0f172a] border border-gray-600 rounded-lg p-3 text-gray-100 focus:outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-gray-400 mb-2">Your Email</label>
            <input type="email" name="email" placeholder="Enter your email"
                   class="w-full bg-[#0f172a] border border-gray-600 rounded-lg p-3 text-gray-100 focus:outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-gray-400 mb-2">Message</label>
            <textarea name="message" rows="5" placeholder="Type your message..."
                      class="w-full bg-[#0f172a] border border-gray-600 rounded-lg p-3 text-gray-100 focus:outline-none focus:border-blue-500"></textarea>
          </div>

          <button type="submit"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300">
            Send Message
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- 🔹 Footer -->
  <footer class="mt-auto text-center text-gray-500 py-6 border-t border-gray-800">
    © {{ date('Y') }} SmartEdu — Powered by Sawo Software Systems
  </footer>

  <script>
    lucide.createIcons();

    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.getElementById('closeMenu');
    const overlay = document.getElementById('overlay');

    menuBtn.addEventListener('click', () => {
      mobileMenu.classList.add('active');
      overlay.classList.add('active');
    });

    const closeMobileMenu = () => {
      mobileMenu.classList.remove('active');
      overlay.classList.remove('active');
    };

    closeMenu.addEventListener('click', closeMobileMenu);
    overlay.addEventListener('click', closeMobileMenu);
  </script>
</body>
</html>
