<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$branch_id = intval($_GET['branch_id'] ?? 0);
$semester  = intval($_GET['semester'] ?? 0);

if (!$branch_id || !$semester) { 
    header("Location: view_scheme.php"); 
    exit; 
}

// Fetch branch
$bq = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$bq->execute([$branch_id]);
$branch = $bq->fetch();
if (!$branch) { header("Location: view_scheme.php"); exit; }

// Fetch parent scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$branch['scheme_id']]);
$scheme = $sq->fetch();

// Fetch courses under this branch + semester
$cq = $pdo->prepare("
    SELECT *
    FROM courses
    WHERE branch_id = ? AND semester = ?
    ORDER BY name
");
$cq->execute([$branch_id, $semester]);
$courses = $cq->fetchAll();

// image fallback
$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Courses — <?= safe($branch['name']) ?> (Sem <?= $semester ?>)</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Schemes</a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $scheme['id'] ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> &rsaquo;
    <a href="view_semesters.php?branch_id=<?= $branch_id ?>" class="hover:underline"><?= safe($branch['name']) ?></a> &rsaquo;
    <span class="font-semibold">Sem <?= $semester ?></span>
  </div>

  <h1 class="text-2xl font-bold mb-4 dark:text-white">
    Courses — <?= safe($branch['name']) ?> (Sem <?= $semester ?>)
  </h1>

  <?php if (empty($courses)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No courses found for this semester.
    </div>
  <?php else: ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

    <?php foreach ($courses as $c): 
      $img = $c['image_path'] ?: $DEFAULT_IMG;
    ?>

      <a href="view_link.php?course_id=<?= $c['id'] ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-xl transition">

        <img   referrerpolicy="no-referrer" src="<?= safe($img) ?>"
             class="w-full h-36 object-cover rounded-lg mb-3"
             onerror="this.src='<?= $DEFAULT_IMG ?>'">

        <div class="text-lg font-semibold dark:text-white">
          <?= safe($c['name']) ?>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Open notes →
        </div>

      </a>

    <?php endforeach; ?>

  </div>

  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
