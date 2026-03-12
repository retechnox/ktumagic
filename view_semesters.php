<?php
include 'db.php';

function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$branch_id = intval($_GET['branch_id'] ?? 0);
if (!$branch_id) { header("Location: view_scheme.php"); exit; }

// Verify signature for anti-scraping
if (!verify_url_sig()) {
    header("Location: index.php");
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
<body class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900">
<?php include 'nav.php'; ?>

<main class="flex-grow">
<div class="max-w-5xl mx-auto px-4 pb-10">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="<?= sign_url('view_scheme.php', []) ?>" class="hover:underline">Schemes</a> &rsaquo;
    <a href="<?= sign_url('view_branch.php', ['scheme_id' => $scheme['id']]) ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> 
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

  <!-- Branch Resources (Syllabus, Calendar, Timetable) -->
  <?php if (!empty($branch['syllabus_link']) || !empty($branch['calendar_link']) || !empty($branch['timetable_link'])): ?>
    <div class="flex flex-wrap gap-4 mb-10 justify-center">
      <?php if (!empty($branch['syllabus_link'])): ?>
        <a href="<?= safe($branch['syllabus_link']) ?>" target="_blank" class="px-6 py-3 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 text-blue-700 dark:text-blue-300 font-bold rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-2 ring-1 ring-blue-200 dark:ring-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          Syllabus
        </a>
      <?php endif; ?>

      <?php if (!empty($branch['calendar_link'])): ?>
        <a href="<?= safe($branch['calendar_link']) ?>" target="_blank" class="px-6 py-3 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 text-indigo-700 dark:text-indigo-300 font-bold rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-2 ring-1 ring-indigo-200 dark:ring-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Calendar
        </a>
      <?php endif; ?>

      <?php if (!empty($branch['timetable_link'])): ?>
        <a href="<?= safe($branch['timetable_link']) ?>" target="_blank" class="px-6 py-3 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-gray-800 dark:to-gray-700 text-purple-700 dark:text-purple-300 font-bold rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-2 ring-1 ring-purple-200 dark:ring-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Timetable
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="flex flex-col items-center justify-center text-center mb-8">
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white font-['Sora'] mb-2">Select Semester</h2>
    <p class="text-gray-500 dark:text-gray-400">Choose your current semester to explore courses and study materials.</p>
  </div>

  <!-- Semester Cards -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php for ($i = 1; $i <= 8; $i++): ?>
      <!-- FIXED FLOW: Now passes branch_id (NOT scheme_id) -->
      <a href="<?= sign_url('view_courses.php', ['branch_id' => $branch_id, 'semester' => $i]) ?>"
         class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center p-8 z-10">
        
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-full group-hover:scale-[2.5] transition-transform duration-500 -z-10"></div>
        
        <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:-rotate-6 transition-transform duration-300">
          <span class="text-2xl font-extrabold font-['Sora'] drop-shadow-sm">S<?= $i ?></span>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Semester <?= $i ?></h3>
        
        <div class="absolute bottom-4 opacity-0 transform translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 text-blue-500 dark:text-blue-400 transition-all duration-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
        </div>
      </a>
    <?php endfor; ?>
  </div>

</div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
