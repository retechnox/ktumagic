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
<meta name="description" content="Access semester-wise academic resources for <?= safe($branch['name']) ?> on KTU Magic. Select your semester to find notes and courses.">
<meta name="keywords" content="KTU Semesters, <?= safe($branch['name']) ?> Semesters, KTU Magic">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
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
    <img   referrerpolicy="no-referrer" src="<?= safe($branch_image) ?>"
         class="w-full h-52 object-cover"
         onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80'">

    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
      <h2 class="text-3xl font-bold text-white drop-shadow-xl">
        <?= safe($branch['name']) ?>
      </h2>
    </div>
  </div>

  <!-- Branch Resources (Syllabus & Calendar) -->
  <?php if (!empty($branch['syllabus_link']) || !empty($branch['calendar_link'])): ?>
  <div class="flex flex-wrap gap-4 mb-8 justify-center mt-[-1rem]">
    <?php if (!empty($branch['syllabus_link'])): ?>
      <a href="<?= safe($branch['syllabus_link']) ?>" target="_blank" class="px-6 py-3 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-semibold rounded-full shadow-lg hover:shadow-xl hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        View Syllabus
      </a>
    <?php endif; ?>

    <?php if (!empty($branch['calendar_link'])): ?>
      <a href="<?= safe($branch['calendar_link']) ?>" target="_blank" class="px-6 py-3 bg-white dark:bg-gray-800 text-purple-600 dark:text-purple-400 font-semibold rounded-full shadow-lg hover:shadow-xl hover:bg-purple-50 dark:hover:bg-gray-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Academic Calendar
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

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
