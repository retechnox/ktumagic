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
/* ====================== GLOBAL UI ======================= */

/* Marquee (top alerts) */
@keyframes marquee {
  0% {transform: translateX(100%);}
  100% {transform: translateX(-100%);}
}
.animate-marquee {animation: marquee 18s linear infinite;}

/* Universal card design (courses + schemes) */
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
  transform: translateY(-6px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.20);
}
.uni-card img {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
}

/* Gradient premium ad card */
.gradient-card {
  background: linear-gradient(to bottom right, #eef4ff, #dce3ff);
}
.dark .gradient-card {
  background: linear-gradient(to bottom right, #2d2d2d, #1a1a1a);
}

/* Modal blur */
.modal-bg { backdrop-filter: blur(6px); }
</style>

</head>

<body class="bg-gray-50 dark:bg-darkBg text-gray-900 dark:text-darkText">

<!-- ================================= NAVBAR ================================= -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-darkBg shadow">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-indigo-600">
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

<!-- ================================= ALERT BAR ================================= -->
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

<!-- ================================= HERO ================================= -->
<section class="pt-20 pb-16 container mx-auto px-6 text-center fade-up">
  <h1 class="text-5xl md:text-6xl font-extrabold">
    Welcome to <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-purple-600">KTU Magic</span>
  </h1>
  <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mt-4">
      Your one-stop destination for <strong>KTU notes, branches, courses & schemes</strong>.
  </p>

  <a href="view_scheme.php" class="inline-block mt-8 px-10 py-3 rounded-full bg-primary text-white font-semibold shadow hover:bg-blue-700">
    Get Started
  </a>
</section>

<!-- ================================= MAIN WRAPPER ================================= -->
<div class="container mx-auto px-6 flex gap-10">

    <!-- LEFT AD -->
    <aside class="hidden lg:block w-64">
      <div class="gradient-card rounded-2xl shadow-xl p-5 sticky top-28">
        <h3 class="font-bold text-lg text-primary mb-2">📢 Sponsored</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
          Promote your course or notes here.
        </p>
        <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=900&q=60"
             class="rounded-xl mt-4">
      </div>
    </aside>

    <!-- ================================= MAIN CONTENT ================================= -->
    <div class="flex-1">

        <!-- FETCH COURSES -->
        <?php
        $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC LIMIT 12")->fetchAll();
        $DEFAULT_IMG = "assets/default_course.jpg";
        ?>

        <h2 id="courses" class="text-3xl font-bold mb-6">Latest Courses</h2>

        <div class="grid md:grid-cols-3 gap-6">
        <?php foreach ($courses as $c):
         $img = (!empty($c['image_path'])) ? $c['image_path'] : $DEFAULT_IMG;
        ?>
          <a href="view_link.php?course_id=<?= $c['id'] ?>" class="uni-card">

            <img src="<?= $img ?>">

            <div class="p-4">
              <h3 class="text-lg font-semibold"><?= htmlspecialchars($c['name']) ?></h3>

              <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                Semester <?= $c['semester'] ?>
              </p>

              <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Branch <?= $c['branch_id'] ?></span>
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">Scheme <?= $c['scheme_id'] ?></span>
              </div>

              <p class="text-primary mt-3 text-sm">Open Course →</p>
            </div>
          </a>
        <?php endforeach; ?>
        </div>

        <!-- ================================= SCHEMES ================================= -->
        <h2 class="text-3xl font-bold mb-6 mt-12">Schemes</h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- 2019 -->
            <a href="view_branch.php?scheme_id=3" class="uni-card p-0">
              <img src="assets/2019/1.jpg" onerror="this.src='assets/default_scheme.jpg'">
              <div class="p-4">
                <h3 class="text-xl font-semibold">2019 Scheme</h3>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Browse Branches →</p>
              </div>
            </a>

            <!-- 2025 -->
            <a href="view_branch.php?scheme_id=4" class="uni-card p-0">
              <img src="assets/2025/1.jpg" onerror="this.src='assets/default_scheme.jpg'">
              <div class="p-4">
                <h3 class="text-xl font-semibold">2025 Scheme</h3>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Browse Branches →</p>
              </div>
            </a>

        </div>

    </div>

    <!-- ================================= RIGHT PREMIUM AD ================================= -->
    <aside class="hidden xl:block w-72">
      <div class="gradient-card rounded-2xl shadow-xl p-5 sticky top-28 animate-fade">

        <h3 class="font-bold text-lg flex items-center gap-2 text-primary">
          🔥 Featured
        </h3>

        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
          Reach thousands of students. Promote your educational product or notes here.
        </p>

        <img src="https://images.unsplash.com/photo-1522199710521-72d69614c702?w=900&q=60"
             class="rounded-xl mt-4">

        <a href="#"
           class="mt-4 block text-center bg-primary text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
          Promote Now
        </a>
      </div>
    </aside>

</div>

<!-- ================================= POPUP MODAL ================================= -->
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

<!-- ================================= JS ================================= -->
<script>
function toggleTheme() {
  const dark = document.documentElement.classList.toggle("dark");
  localStorage.setItem("theme", dark ? "dark" : "light");
}

/* GSAP animations */
window.addEventListener("load", () => {
  gsap.from(".fade-up", {opacity:0, y:40, duration:1, ease:"power3.out"});
  gsap.from(".uni-card", {opacity:0, y:30, scale:0.97, duration:0.9, stagger:0.12, ease:"power2.out"});
});

/* Social modal show once */
if (!localStorage.getItem("socialPopupShown")) {
  setTimeout(() => {
    document.getElementById("socialModal").classList.remove("hidden");
    document.getElementById("socialModal").classList.add("flex");
  }, 1200);
  localStorage.setItem("socialPopupShown", "true");
}

function closeModal() {
  document.getElementById("socialModal").classList.add("hidden");
}
</script>

</body>
</html>
