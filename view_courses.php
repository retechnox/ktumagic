<?php
include 'db.php';

$branch_id = intval($_GET['branch_id'] ?? 0);
$semester  = intval($_GET['semester'] ?? 0);

$branch = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$branch->execute([$branch_id]);
$branch = $branch->fetch();
$scheme_id = $branch['scheme_id'];

$courses = $pdo->prepare("SELECT * FROM courses WHERE branch_id = ? AND semester = ? ORDER BY name");
$courses->execute([$branch_id, $semester]);
$courses = $courses->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Courses</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = { darkMode: 'class' }
function toggleTheme(){ document.documentElement.classList.toggle('dark'); localStorage.setItem("theme",document.documentElement.classList.contains("dark")?"dark":"light"); }
document.addEventListener("DOMContentLoaded",()=>{ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add('dark'); });
</script>

</head>
<body class="bg-gray-100 dark:bg-gray-900">

<!-- NAV -->
<nav class="bg-white dark:bg-gray-800 shadow-md p-4 mb-4">
  <div class="max-w-5xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold dark:text-white">KTU Magic</h1>
    <button onclick="toggleTheme()" class="px-3 py-1 rounded-lg border dark:border-gray-600 text-sm dark:text-gray-300">🌙 / ☀️</button>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-4 pb-10">

  <a href="view_semesters.php?branch_id=<?= $branch_id ?>" class="text-blue-600 dark:text-blue-400">← Back</a>

  <h2 class="text-2xl font-bold mt-3 dark:text-white">
    <?= htmlspecialchars($branch['name']) ?> — Semester <?= $semester ?>
  </h2>

  <div class="grid md:grid-cols-2 gap-6 mt-6">
    <?php foreach ($courses as $c): ?>
      <a href="view_link.php?course_id=<?= $c['id'] ?>"
         class="block bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition p-5">
        <h3 class="text-xl font-semibold dark:text-white"><?= htmlspecialchars($c['name']) ?></h3>
      </a>
    <?php endforeach; ?>

    <?php if (!$courses): ?>
      <p class="text-gray-500 dark:text-gray-300">No courses available.</p>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
