<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$course_id = intval($_GET['course_id'] ?? 0);
if (!$course_id) { header("Location: view_scheme.php"); exit; }

// Fetch course
$cq = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cq->execute([$course_id]);
$course = $cq->fetch();
if (!$course) { header("Location: view_scheme.php"); exit; }

// Fetch branch
$bq = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$bq->execute([$course['branch_id']]);
$branch = $bq->fetch();

// Fetch scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$branch['scheme_id']]);
$scheme = $sq->fetch();

$links = json_decode($course['links'], true) ?: [];

// Convert normal Google Drive link → preview link
function toPreview($url) {
    // Change /view → /preview
    return preg_replace('#/view(\?.*)?$#', '/preview', $url);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= safe($course['name']) ?> — Notes</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
<?php include 'nav.php'; ?>

<div class="max-w-5xl mx-auto px-4 pb-10">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Schemes</a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $scheme['id'] ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> &rsaquo;
    <a href="view_semesters.php?branch_id=<?= $branch['id'] ?>" class="hover:underline"><?= safe($branch['name']) ?></a> &rsaquo;
    <a href="view_courses.php?branch_id=<?= $branch['id'] ?>&semester=<?= $course['semester'] ?>" class="hover:underline">
      Sem <?= $course['semester'] ?>
    </a> &rsaquo;

    <span class="font-semibold"><?= safe($course['name']) ?></span>
  </div>

  <!-- Back -->
  <a href="view_courses.php?branch_id=<?= $branch['id'] ?>&semester=<?= $course['semester'] ?>"
     class="text-blue-600 dark:text-blue-400">
     ← Back to Courses
  </a>

  <h2 class="text-2xl font-bold mt-3 mb-4 dark:text-white">
    <?= safe($course['name']) ?> — Notes
  </h2>

  <div class="space-y-4">

    <?php if ($links): ?>

      <?php foreach ($links as $l): ?>
        <?php
          $orig = $l['url'];
          $preview = toPreview($orig);

          // Extract ID for download URL
          preg_match('#/d/([^/]+)/#', $orig, $m);
          $fileId = $m[1] ?? null;
          $download = $fileId ? "https://drive.google.com/uc?export=download&id=".$fileId : null;
        ?>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow">
          <h3 class="font-semibold text-lg dark:text-white mb-2"><?= safe($l['link_name']) ?></h3>

          <div class="flex items-center gap-6">

            <!-- View -->
            <a href="viewer_embed.php?url=<?= urlencode($preview) ?>"
               target="_blank"
               class="text-blue-600 dark:text-blue-400 underline">
               📄 View
            </a>

            <!-- Download -->
            <?php if ($download): ?>
              <a href="<?= $download ?>" target="_blank"
                 class="text-green-600 dark:text-green-400 underline">
                 ⬇ Download
              </a>
            <?php endif; ?>

          </div>
        </div>

      <?php endforeach; ?>

    <?php else: ?>

      <p class="text-gray-500 dark:text-gray-300">No notes found.</p>

    <?php endif; ?>

  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
