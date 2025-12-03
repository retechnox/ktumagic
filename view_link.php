<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$course_id = intval($_GET['course_id'] ?? 0);
if (!$course_id) { header('Location: view_scheme.php'); exit; }

$q = $pdo->prepare('SELECT c.*, b.name AS branch_name, s.name AS scheme_name FROM courses c LEFT JOIN branches b ON b.id = c.branch_id LEFT JOIN schemes s ON s.id = c.scheme_id WHERE c.id = ?');
$q->execute([$course_id]);
$course = $q->fetch();
if (!$course) { header('Location: view_scheme.php'); exit; }

$links = json_decode($course['links'] ?: '[]', true) ?: [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Links — <?= safe($course['name']) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <!-- Breadcrumbs -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Home</a> &rsaquo;
    <a href="view_semesters.php?scheme_id=<?= $course['scheme_id'] ?>" class="hover:underline"><?= safe($course['scheme_name']) ?></a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $course['scheme_id'] ?>&semester=<?= $course['semester'] ?>" class="hover:underline">Sem <?= $course['semester'] ?></a> &rsaquo;
    <a href="view_courses.php?branch_id=<?= $course['branch_id'] ?>&semester=<?= $course['semester'] ?>" class="hover:underline"><?= safe($course['branch_name']) ?></a> &rsaquo;
    <span class="font-semibold"><?= safe($course['name']) ?></span>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold"><?= safe($course['name']) ?></h1>
    <div class="text-sm text-gray-500 dark:text-gray-400">Semester <?= safe($course['semester']) ?></div>
  </div>

  <?php if (empty($links)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No links saved for this course.
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <?php foreach ($links as $lnk): ?>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="flex items-start justify-between gap-4">
            <div>
              <div class="font-semibold"><?= safe($lnk['link_name'] ?? 'Link') ?></div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 break-words">
                <a href="<?= safe($lnk['url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                  <?= safe($lnk['url'] ?? '') ?>
                </a>
              </div>
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
