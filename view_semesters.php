<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$scheme_id = intval($_GET['scheme_id'] ?? 0);
if (!$scheme_id) { header('Location: view_scheme.php'); exit; }

// fetch scheme
$sq = $pdo->prepare('SELECT * FROM schemes WHERE id = ?');
$sq->execute([$scheme_id]);
$scheme = $sq->fetch();
if (!$scheme) { header('Location: view_scheme.php'); exit; }

// For UX show available semesters based on courses under scheme
$semesters = $pdo->prepare('SELECT DISTINCT semester FROM courses WHERE scheme_id = ? AND semester IS NOT NULL ORDER BY semester');
$semesters->execute([$scheme_id]);
$semesters = $semesters->fetchAll(PDO::FETCH_COLUMN);

// if none, fall back to 1..8
if (empty($semesters)) {
  $semesters = range(1,8);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Semesters — <?= safe($scheme['name']) ?> — KTU Magic</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <!-- Breadcrumbs -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Home</a> &rsaquo;
    <span class="font-semibold"><?= safe($scheme['name']) ?></span>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-3xl font-bold"><?= safe($scheme['name']) ?> — Select Semester</h1>
    <div class="flex items-center gap-2">
      <!-- Quick jump: change scheme -->
      <a href="view_scheme.php" class="text-sm px-3 py-1 border rounded text-gray-700 dark:text-gray-200">Change Scheme</a>
    </div>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
    <?php foreach ($semesters as $sem): ?>
      <a href="view_branch.php?scheme_id=<?= $scheme_id ?>&semester=<?= intval($sem) ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-2xl transition">
        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">Sem <?= intval($sem) ?></div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">View branches for this semester</div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
