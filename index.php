<?php include 'db.php'; ?>
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

<!-- TailwindCSS + GSAP -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: "#2563EB",
        darkBg: "#1A1A1A",
        darkCard: "#242424",
        darkText: "#EAEAEA"
      }
    }
  }
};
</script>

<style>

/* ======================== Marquee ========================= */
@keyframes marquee {
  0% {transform: translateX(100%);}
  100% {transform: translateX(-100%);}
}
.animate-marquee {animation: marquee 18s linear infinite;}

/* ======================== Card UI ========================= */
.uni-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  padding: 0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: .3s ease;
}
.dark .uni-card { background: #242424; }

.uni-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 28px rgba(0,0,0,0.25);
}

.uni-card img {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
  display: block;
}

/* FIX: Scheme cards collapsing bug */
.scheme-card {
  min-height: 330px; /* ensures schemes always show */
}

/* Sponsored Card */
.gradient-card {
  background: linear-gradient(to bottom right, #eef4ff, #dce3ff);
}
.dark .gradient-card {
  background: linear-gradient(to bottom right, #2d2d2d, #1a1a1a);
}
.sticky-ad {
  position: sticky;
  top: 90px;
}

/* ======================== Slider ========================= */
.slider {
  position: relative;
  width: 100%;
  height: 350px;
  overflow: hidden;
  border-radius: 18px;
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 1s ease;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.slide.active {
  opacity: 1;
}

/* ======================== Icons Grid ========================= */
.icon-grid img {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  border-radius: 12px;
  transition: .25s;
}
.icon-grid img:hover {
  transform: translateY(-4px);
}

/* Modal */
.modal-bg { backdrop-filter: blur(6px); }

</style>

</head>

<body class="bg-gray-50 dark:bg-darkBg text-gray-900 dark:text-darkText">

<!-- NAVBAR -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-darkBg shadow">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <a href="/" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-indigo-600">
      KTU Magic
    </a>

    <div class="flex items-center gap-6 text-sm">
      <a href="view_scheme.php" class="hover:text-primary transition">View Notes</a>
      <a href="#courses" class="hover:text-primary transition">Courses</a>
      <button onclick="toggleTheme()" class="p-2 rounded-full bg-gray-200 dark:bg-darkCard">
        <span class="dark:hidden">🌙</span>
        <span class="hidden dark:inline">☀️</span>
      </button>
    </div>
  </div>
</nav>

<!-- ALERT BAR -->
<div class="mt-16 w-full bg-blue-600 text-white py-2 shadow">
  <div class="container mx-auto px-6 flex gap-3">
    <span class="bg-white text-blue-700 px-3 py-1 text-xs rounded-full font-semibold">Alerts</span>
    <div class="overflow-hidden whitespace-nowrap w-full">
      <div class="animate-marquee text-sm">
        2024 Scheme Updated • New Courses Added • Notes Uploading Weekly • Question Banks Refreshed
      </div>
    </div>
  </div>
</div>

<!-- HERO -->
<section class="pt-20 pb-10 container mx-auto px-6 text-center fade-up">
  <h1 class="text-6xl md:text-7xl font-extrabold">
    Welcome to <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-purple-600">KTU Magic</span>
  </h1>
  <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mt-4">
      Your one-stop destination for <strong>KTU notes, branches, courses & schemes</strong>.
  </p>
</section>

<!-- =================== FULL-WIDTH SLIDER =================== -->
<div class="container mx-auto px-6">
  <div class="slider" id="slider">
    <div class="slide active"><img src="assets/slider1.jpg"></div>
    <div class="slide"><img src="assets/slider2.jpg"></div>
  </div>
</div>

<!-- =================== ICON GRID =================== -->
<div class="container mx-auto px-6 mt-10 icon-grid">
  <div class="grid grid-cols-4 gap-4">
    <?php for($i=1;$i<=8;$i++): ?>
      <img src="assets/<?= $i ?>.jpg" class="shadow rounded-xl">
    <?php endfor; ?>
  </div>
</div>

<!-- GIVE SPACE SO SCHEMES DON'T COLLAPSE -->
<div class="mt-16"></div>

<!-- =================== MAIN + SPONSORED (80 / 20) =================== -->
<div class="container mx-auto px-6 flex flex-col lg:flex-row gap-10">

    <!-- MAIN CONTENT (80%) -->
    <div class="flex-1 lg:w-[80%]">

        <?php
        $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC LIMIT 12")->fetchAll();
        $DEFAULT_IMG = "assets/default_course.jpg";
        ?>

        <h2 id="courses" class="text-3xl font-bold mb-6">Latest Courses</h2>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($courses as $c):
            $img = (!empty($c['image_path'])) ? $c['image_path'] : $DEFAULT_IMG;
        ?>
          <a href="view_link.php?course_id=<?= $c['id'] ?>" class="uni-card fade-el">
            <img src="<?= $img ?>">
            <div class="p-4">
              <h3 class="text-lg font-semibold"><?= htmlspecialchars($c['name']) ?></h3>
              <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Semester <?= $c['semester'] ?></p>
              <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Branch <?= $c['branch_id'] ?></span>
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">Scheme <?= $c['scheme_id'] ?></span>
              </div>
              <p class="text-primary mt-3 text-sm">Open Course →</p>
            </div>
          </a>
        <?php endforeach; ?>
        </div>

        <!-- =================== SCHEMES =================== -->
        <h2 class="text-3xl font-bold mb-6 mt-12">Schemes</h2>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">

            <a href="view_branch.php?scheme_id=3" class="uni-card fade-el scheme-card">
              <img src="assets/2019/1.jpg">
              <div class="p-4">
                <h3 class="text-xl font-semibold">2019 Scheme</h3>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Browse Branches →</p>
              </div>
            </a>

            <a href="view_branch.php?scheme_id=4" class="uni-card fade-el scheme-card">
              <img src="assets/2025/1.jpg">
              <div class="p-4">
                <h3 class="text-xl font-semibold">2025 Scheme</h3>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Browse Branches →</p>
              </div>
            </a>

        </div>

    </div>

    <!-- SPONSORED AD RIGHT (20%) -->
    <aside class="w-full lg:w-[20%] sticky-ad fade-el">
      <div class="gradient-card rounded-2xl shadow-xl p-5">

        <h3 class="font-bold text-lg text-primary">📢 Sponsored</h3>

        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
          Promote your course or notes here and reach thousands of students.
        </p>

        <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=900&q=60"
             class="rounded-xl mt-4">

        <a href="#"
           class="mt-4 block text-center bg-primary text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
          Advertise Here
        </a>

      </div>
    </aside>

</div>

<!-- =================== POPUP MODAL =================== -->
<div id="socialModal" class="fixed inset-0 hidden items-center justify-center modal-bg bg-black bg-opacity-40 z-[999]">
  <div class="bg-white dark:bg-darkCard p-6 rounded-xl w-80 shadow-xl text-center">
    <h2 class="font-bold text-xl mb-4">Connect With Us</h2>

    <div class="flex flex-col gap-3">
      <a href="#" class="p-3 bg-pink-600 text-white rounded-lg shadow">Instagram</a>
      <a href="#" class="p-3 bg-green-600 text-white rounded-lg shadow">WhatsApp</a>
      <a href="#" class="p-3 bg-sky-600 text-white rounded-lg shadow">Telegram</a>
      <a href="#" class="p-3 bg-blue-700 text-white rounded-lg shadow">Facebook</a>
    </div>

    <button onclick="closeModal()" class="mt-5 text-sm text-gray-500">Close</button>
  </div>
</div>

<!-- =================== JS =================== -->
<script>

/* ======================== Theme ========================= */
function toggleTheme() {
  const dark = document.documentElement.classList.toggle("dark");
  localStorage.setItem("theme", dark ? "dark" : "light");
}

/* ======================== GSAP FIXED ========================= */
document.addEventListener("DOMContentLoaded", () => {
  const imgs = document.images;
  let loaded = 0;

  function runAnimations() {
    gsap.from(".fade-up", {opacity:0, duration:1});
    gsap.from(".fade-el", {opacity:0, duration:1, stagger:0.15});
  }

  if (imgs.length === 0) runAnimations();

  [...imgs].forEach(img => {
    if (img.complete) {
      loaded++;
      if (loaded === imgs.length) runAnimations();
    } else {
      img.addEventListener("load", () => {
        loaded++;
        if (loaded === imgs.length) runAnimations();
      });
    }
  });
});

/* ======================== AUTO SLIDER ========================= */
let index = 0;
setInterval(() => {
  const slides = document.querySelectorAll("#slider .slide");
  slides.forEach(s => s.classList.remove("active"));
  slides[index].classList.add("active");
  index = (index + 1) % slides.length;
}, 3000);

/* =================== Social Modal Once =================== */
if (!localStorage.getItem("socialPopupShown")) {
  setTimeout(() => {
    document.getElementById("socialModal").classList.remove("hidden");
    document.getElementById("socialModal").classList.add("flex");
  }, 1500);
  localStorage.setItem("socialPopupShown", "true");
}

function closeModal() {
  document.getElementById("socialModal").classList.add("hidden");
}
</script>

</body>
</html>
