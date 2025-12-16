<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$schemes = $pdo->query('SELECT * FROM schemes ORDER BY name')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Schemes — KTU Magic</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <h1 class="text-3xl font-bold mb-3">Schemes</h1>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <?php if (!empty($schemes)): foreach ($schemes as $s): ?>
      <!-- FIX: Removed link to semesters. Now goes to branches -->
      <a href="view_branch.php?scheme_id=<?= $s['id'] ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-lg transition">
        <div class="text-2xl font-semibold"><?= safe($s['name']) ?></div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">View branches</div>
      </a>
    <?php endforeach; else: ?>
      <div class="col-span-full text-center text-gray-500">No schemes found.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
