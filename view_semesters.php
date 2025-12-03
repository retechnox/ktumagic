<?php
include 'db.php';

$branch_id = intval($_GET['branch_id'] ?? 0);
$branch = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$branch->execute([$branch_id]);
$branch = $branch->fetch();
$scheme_id = $branch['scheme_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Semester</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config={darkMode:'class'}
function toggleTheme(){document.documentElement.classList.toggle('dark');localStorage.setItem("theme",document.documentElement.classList.contains("dark")?"dark":"light");}
document.addEventListener("DOMContentLoaded",()=>{if(localStorage.getItem("theme")==="dark")document.documentElement.classList.add('dark');});
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
  <a href="view_branch.php?scheme_id=<?= $scheme_id ?>" class="text-blue-600 dark:text-blue-400">← Back</a>

  <h2 class="text-2xl font-bold mt-2 mb-4 dark:text-white"><?= htmlspecialchars($branch['name']) ?> - Semesters</h2>

  <div class="grid md:grid-cols-4 gap-6">
    <?php for ($i=1; $i<=8; $i++): ?>
      <a href="view_courses.php?branch_id=<?= $branch_id ?>&semester=<?= $i ?>"
         class="block bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition p-8 text-center">
        <h3 class="text-xl font-semibold dark:text-white">Semester <?= $i ?></h3>
      </a>
    <?php endfor; ?>
  </div>
</div>

</body>
</html>
