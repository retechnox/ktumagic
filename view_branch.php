<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$scheme_id = intval($_GET['scheme_id'] ?? 0);
$semester = intval($_GET['semester'] ?? 0);
if (!$scheme_id || !$semester) { header('Location: view_scheme.php'); exit; }

// fetch scheme
$sq = $pdo->prepare('SELECT * FROM schemes WHERE id = ?');
$sq->execute([$scheme_id]);
$scheme = $sq->fetch();
if (!$scheme) { header('Location: view_scheme.php'); exit; }

// Get branches
$q = $pdo->prepare('
  SELECT DISTINCT b.*
  FROM branches b
  JOIN courses c ON c.branch_id = b.id
  WHERE b.scheme_id = ? AND c.scheme_id = ? AND c.semester = ?
  ORDER BY b.name
');
$q->execute([$scheme_id, $scheme_id, $semester]);
$branches = $q->fetchAll();

// fallback image used everywhere
$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Branches — <?= safe($scheme['name']) ?> — Sem <?= $semester ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- Breadcrumbs -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Home</a> &rsaquo;
    <a href="view_semesters.php?scheme_id=<?= $scheme_id ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> &rsaquo;
    <span class="font-semibold">Sem <?= $semester ?></span>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Branches — Sem <?= $semester ?></h1>
    <div class="flex items-center gap-2">
      <a href="view_semesters.php?scheme_id=<?= $scheme_id ?>" class="text-sm px-3 py-1 border rounded text-gray-700 dark:text-gray-200">Change Semester</a>
      <a href="view_scheme.php" class="text-sm px-3 py-1 border rounded text-gray-700 dark:text-gray-200">Change Scheme</a>
    </div>
  </div>

  <?php if (empty($branches)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No branches found for Sem <?= $semester ?> under this scheme.
    </div>
  <?php else: ?>
  
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

      <?php foreach ($branches as $b): 
        $img = !empty($b['image_path']) ? $b['image_path'] : $DEFAULT_IMG;
      ?>

        <a href="view_courses.php?branch_id=<?= $b['id'] ?>&semester=<?= $semester ?>"
           class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-xl transition">

          <img src="<?= safe($img) ?>" 
               class="w-full h-36 object-cover rounded-lg mb-3"
               onerror="this.src='<?= $DEFAULT_IMG ?>'">

          <div class="text-lg font-semibold"><?= safe($b['name']) ?></div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Open courses for sem <?= $semester ?>
          </div>

        </a>

      <?php endforeach; ?>

    </div>

  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
