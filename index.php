<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>KTU Magic – Notes & Resources</title>

<style>
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  }
</style>

<script src="https://cdn.tailwindcss.com"></script>

<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          // Google-style primary (Indigo/Blue)
          'primary-500': '#4F46E5', // Indigo-600
          'primary-600': '#4338CA', // Indigo-700
          'on-surface': '#202124', // Near black for light mode text
          
          // Custom dark mode colors for a near-black experience
          'dark-surface': '#1A1A1A', // A slightly off-black for the body/main background
          'dark-card': '#242424',    // Darker surface for cards
          'dark-text': '#EAEAEA',    // Light text on dark background
        },
        boxShadow: {
          // Subtle, multi-layered shadow for Material Design elevation
          'google': '0 1px 3px rgba(60,64,67,.3), 0 4px 8px rgba(60,64,67,.15)',
          'google-dark': '0 1px 3px rgba(0,0,0,.4), 0 4px 8px rgba(0,0,0,.2)',
        }
      }
    }
  }

  function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  }

  // Load saved theme
  document.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.classList.add('dark');
    }
  });
</script>

<style>
/* Replaced original .glass with a better card style */
.card-base {
  background-color: white;
  transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px rgba(60,64,67,.3), 0 4px 8px rgba(60,64,67,.15); /* Initial elevation */
}
.card-base:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 8px rgba(60,64,67,.3), 0 8px 16px rgba(60,64,67,.15); /* Higher elevation on hover */
}

/* Dark mode specific styles */
.dark .card-base {
  background-color: #242424; /* dark-card */
  box-shadow: 0 1px 3px rgba(0,0,0,.4), 0 4px 8px rgba(0,0,0,.2);
}
.dark .card-base:hover {
  box-shadow: 0 4px 8px rgba(0,0,0,.5), 0 8px 16px rgba(0,0,0,.3);
}

/* For the Navbar to be solid and elevated in Google-style */
.navbar-base {
    background-color: white;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,.1);
}
.dark .navbar-base {
    background-color: #1A1A1A;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,.3);
}
</style>
</head>

<body class="bg-gray-50 dark:bg-dark-surface text-on-surface dark:text-dark-text min-h-screen">

<nav class="navbar-base fixed w-full top-0 left-0 z-50 py-4 border-b dark:border-gray-700">
  <div class="container mx-auto px-6 flex justify-between items-center">
    
    <a href="index.php" class="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary-500 to-indigo-700 dark:from-indigo-400 dark:to-purple-400">
      KTU Magic
    </a>

    <div class="flex items-center gap-6">
      <a href="view_scheme.php" class="text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 transition font-medium text-sm">View Notes</a>
      <a href="#about" class="text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 transition font-medium text-sm">About</a>
      
      <button onclick="toggleTheme()" class="p-2 rounded-full text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
        <span class="dark:hidden">🌙</span>
        <span class="hidden dark:inline">☀️</span>
      </button>
    </div>

  </div>
</nav>

<section class="pt-32 pb-20 text-center container mx-auto px-6">
  <h1 class="text-6xl font-extrabold mb-6 text-on-surface dark:text-dark-text">
    Welcome to <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-500 to-purple-600">KTU Magic</span>
  </h1>

  <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
    Your one-stop destination for **KTU notes, question banks, schemes, branches, and courses**. Access resources for faster learning and better results.
  </p>

  <a href="view_scheme.php"
    class="mt-10 inline-block px-10 py-3 rounded-full bg-primary-500 text-white font-semibold hover:bg-primary-600 shadow-lg hover:shadow-xl transition text-lg">
    Get Started
  </a>
</section>

<section class="container mx-auto px-6 pb-24">
  <div class="grid md:grid-cols-3 gap-6">

    <div class="card-base p-6 rounded-xl text-center">
      <h3 class="text-xl font-semibold mb-2 text-on-surface dark:text-dark-text">Schemes</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">Browse KTU schemes and navigate into courses easily with structured content.</p>
      <a href="view_scheme.php" class="text-primary-500 font-semibold text-sm hover:text-primary-600 transition">Explore Schemes →</a>
    </div>

    <div class="card-base p-6 rounded-xl text-center">
      <h3 class="text-xl font-semibold mb-2 text-on-surface dark:text-dark-text">Branches</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">Select your department and access all semester notes and resources in one place.</p>
      <a href="view_scheme.php" class="text-primary-500 font-semibold text-sm hover:text-primary-600 transition">Browse Branches →</a>
    </div>

    <div class="card-base p-6 rounded-xl text-center">
      <h3 class="text-xl font-semibold mb-2 text-on-surface dark:text-dark-text">Resources</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">Find question papers, curated reference books, and detailed lecture notes.</p>
      <a href="view_scheme.php" class="text-primary-500 font-semibold text-sm hover:text-primary-600 transition">Check Resources →</a>
    </div>

  </div>
</section>

<section id="about" class="container mx-auto px-6 pb-20 text-center border-t pt-10 border-gray-200 dark:border-gray-800">
  <h2 class="text-3xl font-bold mb-4 text-on-surface dark:text-dark-text">About KTU Magic</h2>
  <p class="text-gray-600 dark:text-gray-400 max-w-3xl mx-auto leading-relaxed">
    KTU Magic is a platform dedicated to making course material and study resources easily accessible for students. We provide a clean interface, structured course navigation, and fast access to semester-wise notes, helping you focus more on learning.
  </p>
</section>

<footer class="bg-gray-100 dark:bg-dark-card py-8 mt-10">
  <div class="container mx-auto px-6 text-center text-gray-600 dark:text-gray-400">
    <p class="font-medium text-sm">KTU Magic © <?= date("Y") ?></p>
    <p class="text-xs mt-1">Your trusted study companion for KTU resources.</p>
    <p class="text-xs mt-3 text-gray-500 dark:text-gray-500">Note: This website is an independent educational resource and is not officially affiliated with KTU.</p>
  </div>
</footer>

</body>
</html>