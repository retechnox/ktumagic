<?php
include 'db.php';

$course_id = intval($_GET['course_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

$links = json_decode($course['links'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($course['name']) ?> Notes</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = { darkMode:'class' }
function toggleTheme(){
  document.documentElement.classList.toggle('dark');
  localStorage.setItem("theme",
    document.documentElement.classList.contains("dark") ? "dark" : "light"
  );
}
document.addEventListener("DOMContentLoaded",()=>{
  if(localStorage.getItem("theme")==="dark")
    document.documentElement.classList.add('dark');
});
</script>

</head>
<body class="bg-gray-100 dark:bg-gray-900">

<!-- NAV -->
<nav class="bg-white dark:bg-gray-800 shadow-md p-4 mb-4">
  <div class="max-w-5xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold dark:text-white">KTU Magic</h1>
    <button onclick="toggleTheme()"
            class="px-3 py-1 rounded-lg border dark:border-gray-600 text-sm dark:text-gray-300">
      🌙 / ☀️
    </button>
  </div>
</nav>

<div class="max-w-5xl mx-auto px-4 pb-10">

  <a href="view_courses.php?branch_id=<?= $course['branch_id'] ?>&semester=<?= $course['semester'] ?>"
     class="text-blue-600 dark:text-blue-400">← Back</a>

  <h2 class="text-2xl font-bold mt-3 dark:text-white">
    <?= htmlspecialchars($course['name']) ?> - Notes
  </h2>

  <div class="mt-6 space-y-4">

    <?php foreach ($links as $l): ?>

      <?php
        // ORIGINAL URL
        $original = $l['url'];

        // PREVIEW URL for iframe
        $preview_url = preg_replace("#/view(\?.*)?$#", "/preview", $original);

        // EXTRACT DRIVE ID
        preg_match("#/d/([^/]+)/#", $original, $m);
        $id = $m[1] ?? null;

        // DIRECT DOWNLOAD URL
        $download_url = $id ? "https://drive.google.com/uc?export=download&id=" . $id : null;
      ?>

      <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="font-semibold text-lg dark:text-white mb-2">
          <?= htmlspecialchars($l['link_name']) ?>
        </h3>

        <div class="flex items-center gap-4">

          <!-- VIEW BUTTON -->
          <a href="viewer_embed.php?url=<?= urlencode($preview_url) ?>"
             class="text-blue-600 dark:text-blue-400 underline"
             target="_blank">
             📄 View
          </a>

          <!-- DOWNLOAD BUTTON -->
          <?php if ($download_url): ?>
            <a href="<?= $download_url ?>"
               class="text-green-600 dark:text-green-400 underline"
               target="_blank">
               ⬇ Download
            </a>
          <?php endif; ?>

        </div>

      </div>

    <?php endforeach; ?>

    <?php if (!$links): ?>
      <p class="text-gray-500 dark:text-gray-300">No notes yet.</p>
    <?php endif; ?>

  </div>

</div>

</body>
</html>
