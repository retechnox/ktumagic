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
    if (localStorage.getItem('ktu-theme') === 'dark') {
      document.documentElement.classList.add('dark');
    }

    // GSAP animations if elements exist
    if (document.querySelector(".hero-title")) {
        gsap.from(".hero-title", {opacity: 0, y: 40, duration: 1});
        gsap.from(".hero-sub", {opacity: 0, y: 40, delay: 0.2, duration: 1});
        gsap.from(".hero-btn", {opacity: 0, y: 40, delay: 0.4, duration: 1});
    }
});
</script>

</head>

<body class="bg-gray-50 dark:bg-darkBg text-black dark:text-darkText min-h-screen">

<!-- Legacy NAVBAR removed. Use include 'nav.php' instead. -->
