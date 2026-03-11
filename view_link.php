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

$links = json_decode($course['links'] ?? '[]', true) ?: [];
$modules = json_decode($course['modules'] ?? '[]', true) ?: [];
$qp_answers = json_decode($course['qp_answers'] ?? '[]', true) ?: [];
$pyqs_data = json_decode($course['pyqs'] ?? '[]', true) ?: [];

// Convert normal Google Drive link → preview link
function toPreview($url) {
    // Change /view → /preview
    return preg_replace('#/view(\?.*)?$#', '/preview', $url ?? '');
}

function renderResourceCard($l, $colorClass = 'border-blue-600') {
    $orig = $l['url'];
    $preview = toPreview($orig);

    // Extract ID for download URL
    preg_match('#/d/([^/]+)/#', $orig, $m);
    $fileId = $m[1] ?? null;
    $download = $fileId ? "https://drive.google.com/uc?export=download&id=".$fileId : null;
    $name = safe($l['link_name']);
    
    $html = '<div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 border-l-8 '.$colorClass.' flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:shadow-md hover:border-l-[12px]">';
    $html .= '<div>';
    $html .= '<h3 class="font-bold text-gray-800 dark:text-white text-lg mb-1">'.$name.'</h3>';
    $html .= '<p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Academic Resource • Google Drive</p>';
    $html .= '</div>';
    $html .= '<div class="flex items-center gap-3">';
    $html .= '<a href="viewer_embed.php?url='.urlencode($preview).'" target="_blank" class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all transform hover:-translate-y-0.5 shadow-lg shadow-blue-500/25 active:scale-95 whitespace-nowrap">';
    $html .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
    $html .= 'View</a>';
    if ($download) {
        $html .= '<a href="'.$download.'" target="_blank" class="flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all transform hover:-translate-y-0.5 shadow-lg shadow-emerald-500/25 active:scale-95 whitespace-nowrap">';
        $html .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
        $html .= 'Download</a>';
    }
    $html .= '</div></div>';
    return $html;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= safe($course['name']) ?> — Notes</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Access and download academic notes and resources for <?= safe($course['name']) ?> on KTU Magic.">
<meta name="keywords" content="<?= safe($course['name']) ?> Notes, KTU Notes, KTU Magic">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
<?php include 'nav.php'; ?>

<br /> <br />
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

    <!-- MODULES SECTION -->
    <?php if ($modules): ?>
      <h2 class="text-2xl font-black mt-10 mb-6 text-blue-600 dark:text-blue-400 uppercase font-['Sora'] tracking-tight">
        Study Modules
      </h2>
      <div class="grid grid-cols-1 gap-4">
        <?php foreach ($modules as $m): echo renderResourceCard($m, 'border-blue-600'); endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- QP & ANSWERS SECTION -->
    <?php if ($qp_answers): ?>
      <h2 class="text-2xl font-black mt-10 mb-6 text-purple-600 dark:text-purple-400 uppercase font-['Sora'] tracking-tight">
        QP & Answer Keys
      </h2>
      <div class="grid grid-cols-1 gap-4">
        <?php foreach ($qp_answers as $q): echo renderResourceCard($q, 'border-purple-600'); endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- PYQS SECTION -->
    <?php if ($pyqs_data): ?>
      <h2 class="text-2xl font-black mt-10 mb-6 text-indigo-600 dark:text-indigo-400 uppercase font-['Sora'] tracking-tight">
        Previous Year Questions
      </h2>
      <div class="grid grid-cols-1 gap-4">
        <?php foreach ($pyqs_data as $p): echo renderResourceCard($p, 'border-indigo-600'); endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- OTHER LINKS SECTION -->
    <?php if ($links): ?>
      <!-- <h2 class="text-2xl font-black mt-10 mb-6 text-gray-600 dark:text-gray-400 uppercase font-['Sora'] tracking-tight">
        Other Resources
      </h2> -->
      <div class="grid grid-cols-1 gap-4">
        <?php foreach ($links as $l): echo renderResourceCard($l, 'border-gray-500'); endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!$modules && !$qp_answers && !$pyqs_data && !$links): ?>
      <div class="py-20 text-center">
        <p class="text-gray-500 dark:text-gray-400 text-lg">No academic resources found for this course yet.</p>
      </div>
    <?php endif; ?>

  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
