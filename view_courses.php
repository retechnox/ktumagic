<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$branch_id = intval($_GET['branch_id'] ?? 0);
$semester = intval($_GET['semester'] ?? 0);
if (!$branch_id || !$semester) { header('Location: view_scheme.php'); exit; }

// fetch branch
$br = $pdo->prepare('SELECT * FROM branches WHERE id = ?');
$br->execute([$branch_id]);
$branch = $br->fetch();
if (!$branch) { header('Location: view_scheme.php'); exit; }

// fetch scheme
$sq = $pdo->prepare('SELECT * FROM schemes WHERE id = ?');
$sq->execute([$branch['scheme_id']]);
$scheme = $sq->fetch();

// fetch courses
$cs = $pdo->prepare('SELECT * FROM courses WHERE branch_id = ? AND semester = ? ORDER BY name');
$cs->execute([$branch_id, $semester]);
$courses = $cs->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Courses — <?= safe($branch['name']) ?> Sem <?= $semester ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <!-- Breadcrumbs -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Home</a> &rsaquo;
    <a href="view_semesters.php?scheme_id=<?= $branch['scheme_id'] ?>" class="hover:underline"><?= safe($scheme['name'] ?? 'Scheme') ?></a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $branch['scheme_id'] ?>&semester=<?= $semester ?>" class="hover:underline">Sem <?= $semester ?></a> &rsaquo;
    <span class="font-semibold"><?= safe($branch['name']) ?></span>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold"><?= safe($branch['name']) ?> — Sem <?= $semester ?></h1>
    <div class="flex items-center gap-2">
      <a href="view_branch.php?scheme_id=<?= $branch['scheme_id'] ?>&semester=<?= $semester ?>" class="text-sm px-3 py-1 border rounded text-gray-700 dark:text-gray-200">Change Branch</a>
      <a href="view_semesters.php?scheme_id=<?= $branch['scheme_id'] ?>" class="text-sm px-3 py-1 border rounded text-gray-700 dark:text-gray-200">Change Semester</a>
    </div>
  </div>

  <?php if (empty($courses)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">No courses found.</div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php foreach ($courses as $c): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow hover:shadow-xl transition">
          <?php if (!empty($c['image_path'])): ?>
            <img src="<?= safe($c['image_path']) ?>" class="w-full h-36 object-cover rounded-lg mb-3" onerror="this.style.display='none'">
          <?php endif; ?>
          <div class="flex items-start justify-between">
            <div>
              <div class="font-semibold text-lg"><?= safe($c['name']) ?></div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Semester <?= safe($c['semester']) ?></div>
            </div>
            <div class="text-right">
              <a href="view_link.php?course_id=<?= $c['id'] ?>" class="inline-block bg-indigo-600 text-white px-3 py-1 rounded text-sm">Open</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
