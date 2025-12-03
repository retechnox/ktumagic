<?php
include 'db.php';

$branch_id = intval($_GET['branch_id'] ?? 0);
$branch = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$branch->execute([$branch_id]);
$branch = $branch->fetch();
$scheme_id = $branch['scheme_id'];

// Use DB image or fallback aesthetic banner
$branch_image = $branch['image_path'] ?: 
  "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1600&q=80";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Semester</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>

<!-- Tailwind Config -->
<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      animation: {
        gradient: "gradientBG 6s ease infinite",
        fadeIn: "fadeIn 1s ease forwards",
      },
      keyframes: {
        gradientBG: {
          "0%": { backgroundPosition: "0% 50%" },
          "50%": { backgroundPosition: "100% 50%" },
          "100%": { backgroundPosition: "0% 50%" }
        },
        fadeIn: {
          "0%": { opacity: 0, transform: "translateY(10px)" },
          "100%": { opacity: 1, transform: "translateY(0)" }
        }
      }
    }
  }
}

function toggleTheme(){
  document.documentElement.classList.toggle('dark');
  localStorage.setItem("theme",
    document.documentElement.classList.contains('dark') ? "dark" : "light"
  );
}

document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    document.documentElement.classList.add('dark');
  }
});
</script>

<!-- Glass effect -->
<style>
.glass {
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}
</style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

<!-- NAV -->
<nav class="bg-white dark:bg-gray-800 shadow-md p-4 mb-6">
  <div class="max-w-5xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold dark:text-white">KTU Magic</h1>
    <button onclick="toggleTheme()" 
      class="px-3 py-1 rounded-lg border dark:border-gray-600 text-sm dark:text-gray-300">
      🌙 / ☀️
    </button>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-4">

  <!-- 🔥 NEW MODERN ANIMATED BANNER -->
  <div class="relative rounded-3xl overflow-hidden shadow-2xl mb-10 animate-fadeIn">

    <!-- Background Image -->
    <img src="<?= htmlspecialchars($branch_image) ?>"
      onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1600&q=80'"
      class="w-full h-60 md:h-72 object-cover opacity-70">

    <!-- Animated Gradient Border -->
    <div class="absolute inset-0 border-4 border-transparent 
                rounded-3xl bg-gradient-to-r from-purple-500 via-pink-400 to-blue-500
                bg-[length:300%_300%] animate-gradient opacity-40">
    </div>

    <!-- Glass Overlay -->
    <div class="absolute inset-0 glass bg-black/30 flex flex-col justify-center px-8 md:px-12">

      <h2 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-xl mb-2">
        <?= htmlspecialchars($branch['name']) ?>
      </h2>

      <p class="text-white/80 text-lg tracking-wide font-light">
        Explore subjects and notes by semester
      </p>

    </div>
  </div>


  <!-- Back -->
  <a href="view_branch.php?scheme_id=<?= $scheme_id ?>" 
     class="text-blue-600 dark:text-blue-400">← Back</a>

  <!-- Section Title -->
  <h2 class="text-3xl font-bold mt-4 mb-6 dark:text-white">Select Semester</h2>


  <!-- 🔥 PREMIUM SEMESTER GRID -->
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 pb-14">

    <?php
      // Beautiful icons for semesters
      $icons = [
        1 => "📘",
        2 => "🧪",
        3 => "📙",
        4 => "📐",
        5 => "🛠️",
        6 => "🧠",
        7 => "💡",
        8 => "🎓",
      ];

      for ($i=1; $i<=8; $i++):
    ?>

    <a href="view_courses.php?branch_id=<?= $branch_id ?>&semester=<?= $i ?>"
       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg border 
              dark:border-gray-700 text-center p-8 transition 
              hover:-translate-y-2 hover:shadow-2xl hover:border-blue-500
              dark:hover:border-blue-500 relative overflow-hidden">

      <!-- Card glow effect -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-purple-500/0 
                  group-hover:from-blue-500/10 group-hover:to-purple-500/10 transition">
      </div>

      <div class="text-5xl mb-4"><?= $icons[$i] ?></div>

      <h3 class="text-xl font-semibold dark:text-white">
        Semester <?= $i ?>
      </h3>

      <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
        View all subjects →
      </p>
    </a>

    <?php endfor; ?>

  </div>

</div>

</body>
</html>
