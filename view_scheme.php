<?php
include 'db.php';
$schemes = $pdo->query("SELECT * FROM schemes ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Choose Scheme - KTU Magic</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
  darkMode: 'class'
}

function toggleTheme() {
  document.documentElement.classList.toggle("dark");
  localStorage.setItem("theme", 
    document.documentElement.classList.contains("dark") ? "dark" : "light"
  );
}

document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    document.documentElement.classList.add("dark");
  }
});
</script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

<!-- NAV -->
<nav class="bg-white dark:bg-gray-800 shadow-md p-4 mb-6">
  <div class="max-w-5xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold dark:text-white">KTU Magic</h1>
    <div class="flex items-center gap-4">
      <button onclick="toggleTheme()" class="px-3 py-1 rounded-lg border dark:border-gray-600 text-sm dark:text-gray-300">
        🌙 / ☀️
      </button>
    </div>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-4 pb-10">
  <h2 class="text-2xl font-bold mb-4 dark:text-white">Choose a Scheme</h2>

  <div class="grid md:grid-cols-3 gap-6">
    <?php foreach ($schemes as $s): ?>
      <a href="view_branch.php?scheme_id=<?= $s['id'] ?>" 
         class="block bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition p-4 text-center">
        <h3 class="text-lg font-semibold dark:text-white"><?= htmlspecialchars($s['name']) ?></h3>
      </a>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
