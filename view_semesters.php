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
  <div class="relative rounded-[2rem] overflow-hidden shadow-2xl mb-12 group">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
    <img referrerpolicy="no-referrer" src="<?= safe($branch_image) ?>"
         class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-700"
         onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80'">

    <div class="absolute inset-0 flex flex-col items-center justify-center z-20 p-6 text-center">
      <span class="px-4 py-1 bg-blue-600/30 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full mb-4 tracking-widest uppercase">
        <?= safe($scheme['name']) ?>
      </span>
      <h2 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-2xl font-['Sora'] leading-tight">
        <?= safe($branch['name']) ?>
      </h2>
    </div>
  </div>

  <!-- Quick Links Section -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-16">
    <?php if (!empty($branch['syllabus_link'])): ?>
      <a href="<?= safe($branch['syllabus_link']) ?>" target="_blank" 
         class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div>
          <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Resource</p>
          <h4 class="font-bold text-gray-800 dark:text-white">Syllabus</h4>
        </div>
      </a>
    <?php endif; ?>

    <?php if (!empty($branch['calendar_link'])): ?>
      <a href="<?= safe($branch['calendar_link']) ?>" target="_blank" 
         class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-indigo-500 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
          <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Academic</p>
          <h4 class="font-bold text-gray-800 dark:text-white">Calendar</h4>
        </div>
      </a>
    <?php endif; ?>

    <?php if (!empty($branch['timetable_link'])): ?>
      <a href="<?= safe($branch['timetable_link']) ?>" target="_blank" 
         class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-purple-500 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
          <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Schedule</p>
          <h4 class="font-bold text-gray-800 dark:text-white">Timetable</h4>
        </div>
      </a>
    <?php endif; ?>
  </div>

  <div class="flex flex-col items-center justify-center text-center mb-10">
    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white font-['Sora'] mb-4">Choose Semester</h2>
    <div class="w-20 h-1.5 bg-blue-600 rounded-full mb-6"></div>
    <p class="text-gray-500 dark:text-gray-400 max-w-lg">Access study materials, previous question papers, and essential notes curated for your specific semester.</p>
  </div>

  <!-- Semester Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php for ($i = 1; $i <= 8; $i++): ?>
      <a href="<?= sign_url('view_courses.php', ['branch_id' => $branch_id, 'semester' => $i]) ?>"
         class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col items-center justify-center text-center">
        
        <!-- Hover Background Glow -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        
        <div class="relative mb-6">
          <div class="absolute inset-0 bg-blue-600 blur-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>
          <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white rounded-[2rem] flex items-center justify-center shadow-xl group-hover:rotate-6 transition-all duration-500 relative z-10">
            <span class="text-4xl font-extrabold font-['Sora']">S<?= $i ?></span>
          </div>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2 group-hover:text-blue-600 transition-colors">Semester <?= $i ?></h3>
        <p class="text-gray-400 text-sm font-medium">Explore Notes & PYQs</p>
        
        <div class="mt-6 w-12 h-12 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </div>
      </a>
    <?php endfor; ?>
  </div>

</div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
