<?php
include 'db.php';

function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$scheme_id = intval($_GET['scheme_id'] ?? 0);
if (!$scheme_id) { header("Location: view_scheme.php"); exit; }

// Fetch scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$scheme_id]);
$scheme = $sq->fetch();
if (!$scheme) { header("Location: view_scheme.php"); exit; }

// Fetch branches for this scheme
$stmt = $pdo->prepare("SELECT * FROM branches WHERE scheme_id = ? ORDER BY name");
$stmt->execute([$scheme_id]);
$branches = $stmt->fetchAll();

$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= safe($scheme['name']) ?> — Branches</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Schemes</a> &rsaquo;
    <span class="font-semibold"><?= safe($scheme['name']) ?></span>
  </div>

  <h1 class="text-2xl font-bold mb-4 dark:text-white">
    Branches — <?= safe($scheme['name']) ?>
  </h1>

  <?php if (empty($branches)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No branches found for this scheme.
    </div>

  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

      <?php foreach ($branches as $b): 
        $img = $b['image_path'] ?: $DEFAULT_IMG;
      ?>

      <!-- FIXED FLOW: Branch now goes to semesters based on BRANCH -->
      <a href="view_semesters.php?branch_id=<?= $b['id'] ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-xl transition">

        <img src="<?= safe($img) ?>"
             class="w-full h-36 object-cover rounded-lg mb-3"
             onerror="this.src='<?= $DEFAULT_IMG ?>'">

        <div class="text-lg font-semibold dark:text-white">
          <?= safe($b['name']) ?>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          View semesters →
        </div>
      </a>

      <?php endforeach; ?>

    </div>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
