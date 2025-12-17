<?php
header("Cache-Control: no-transform");
header("Content-Encoding: none");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>KTU Magic – Notes & Resources</title>

<link rel="icon" type="image/png" href="assets/favicon.png">

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#4F46E5',
        primaryDark: '#4338CA',
        darkBg: '#1A1A1A',
        darkCard: '#242424',
        darkText: '#EAEAEA',
      }
    }
  }
};

document.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.classList.add('dark');
    }

    gsap.from(".hero-title", {opacity: 0, y: 40, duration: 1});
    gsap.from(".hero-sub", {opacity: 0, y: 40, delay: 0.2, duration: 1});
    gsap.from(".hero-btn", {opacity: 0, y: 40, delay: 0.4, duration: 1});
});
</script>

</head>

<body class="bg-gray-50 dark:bg-darkBg text-black dark:text-darkText min-h-screen">

<!-- NAVBAR -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-darkBg shadow">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    
    <a href="index.php" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-purple-600">
      KTU Magic
    </a>

    <div class="flex gap-6 items-center text-sm font-medium">
      <a href="view_scheme.php" class="hover:text-primary transition">Schemes</a>
      <a href="branches.php" class="hover:text-primary transition">Branches</a>
      <a href="#gallery" class="hover:text-primary transition">Gallery</a>

      <button onclick="document.documentElement.classList.toggle('dark'); 
              localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark':'light');"
        class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
        <span class="dark:hidden">🌙</span>
        <span class="hidden dark:inline">☀️</span>
      </button>
    </div>

  </div>
</nav>

<!-- ALERT SCROLLER -->
<div class="mt-16 bg-indigo-600 text-white py-2 overflow-hidden">
  <div class="animate-marquee whitespace-nowrap text-sm">
    <span class="mx-8">📢 New notes for 2024 Scheme will be added soon</span>
    <span class="mx-8">📌 Updated branches now available</span>
    <span class="mx-8">📝 Question bank collections are being updated</span>
  </div>
</div>

<!-- HERO SECTION -->
<section class="relative w-full h-[65vh] flex items-center justify-center text-center">

  <img referrerpolicy="no-referrer" src="assets/header.jpg" class="absolute inset-0 w-full h-full object-cover opacity-80">

  <div class="absolute inset-0 bg-gradient-to-r from-blue-700/60 to-purple-700/60"></div>

  <div class="relative z-10 max-w-3xl px-6">
    <h1 class="hero-title text-5xl md:text-6xl font-extrabold text-white drop-shadow">
      KTU Magic
    </h1>

    <p class="hero-sub text-lg md:text-xl mt-4 text-gray-200">
      Your clean, modern, academic resource hub for schemes, branches, courses & notes.
    </p>

    <a href="view_scheme.php"
       class="hero-btn inline-block mt-8 px-10 py-3 rounded-full bg-white text-primary font-semibold shadow-lg hover:shadow-2xl transition">
      Explore Schemes
    </a>
  </div>
</section>
