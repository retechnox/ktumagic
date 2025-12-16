<?php
include 'db.php';

function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$branch_id = intval($_GET['branch_id'] ?? 0);
if (!$branch_id) { header("Location: view_scheme.php"); exit; }

// Fetch branch
$bq = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$bq->execute([$branch_id]);
$branch = $bq->fetch();
if (!$branch) { header("Location: view_scheme.php"); exit; }

// Fetch parent scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$branch['scheme_id']]);
$scheme = $sq->fetch();

$branch_image = $branch['image_path'] ?: 
    "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= safe($branch['name']) ?> — Semesters</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
<?php include 'nav.php'; ?>

<div class="max-w-5xl mx-auto px-4 pb-10">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Schemes</a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $scheme['id'] ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> 
    &rsaquo;
    <span class="font-semibold"><?= safe($branch['name']) ?></span>
  </div>

  <!-- Header banner -->
  <div class="relative rounded-2xl overflow-hidden shadow-lg mb-6">
    <img src="<?= safe($branch_image) ?>"
         class="w-full h-52 object-cover"
         onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80'">

    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
      <h2 class="text-3xl font-bold text-white drop-shadow-xl">
        <?= safe($branch['name']) ?>
      </h2>
    </div>
  </div>

  <h2 class="text-2xl font-bold mb-4 dark:text-white">Select Semester</h2>

  <!-- Semester Cards -->
  <div class="grid md:grid-cols-4 gap-6">

    <?php for ($i = 1; $i <= 8; $i++): ?>
      <!-- FIXED FLOW: Now passes branch_id (NOT scheme_id) -->
      <a href="view_courses.php?branch_id=<?= $branch_id ?>&semester=<?= $i ?>"
         class="block bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-xl p-8 text-center border dark:border-gray-700 transition">
         
        <h3 class="text-xl font-semibold dark:text-white">Semester <?= $i ?></h3>
      </a>
    <?php endfor; ?>

  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
