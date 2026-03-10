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

// Fetch semester resources
$resQ = $pdo->prepare('SELECT * FROM semester_resources WHERE branch_id = ? AND semester = ?');
$resQ->execute([$branch_id, $semester]);
$sem_res = $resQ->fetch();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Courses — <?= safe($branch['name']) ?> (Sem <?= $semester ?>)</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Browse courses and study materials for <?= safe($branch['name']) ?> Semester <?= $semester ?> on KTU Magic.">
<meta name="keywords" content="KTU Courses, <?= safe($branch['name']) ?> Courses, KTU Magic">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- Breadcrumb -->
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="view_scheme.php" class="hover:underline">Schemes</a> &rsaquo;
    <a href="view_branch.php?scheme_id=<?= $scheme['id'] ?>" class="hover:underline"><?= safe($scheme['name']) ?></a> &rsaquo;
    <a href="view_semesters.php?branch_id=<?= $branch_id ?>" class="hover:underline"><?= safe($branch['name']) ?></a> &rsaquo;
    <span class="font-semibold">Sem <?= $semester ?></span>
  </div>

  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
      </div>
      <div>
        <h1 class="text-2xl font-black dark:text-white uppercase font-['Sora'] tracking-tight">
          <?= safe($branch['name']) ?>
          <span class="text-blue-600 dark:text-blue-400 block text-sm font-bold tracking-widest mt-1">SEMESTER <?= $semester ?></span>
        </h1>
      </div>
    </div>
    
    <div class="relative w-full md:w-80 group">
      <input type="text" id="courseSearch" placeholder="Filter courses..." 
             class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
      <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      </div>

  <?php if ($sem_res): ?>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <?php if ($sem_res['syllabus_link']): ?>
        <a href="<?= safe($sem_res['syllabus_link']) ?>" target="_blank" 
           class="flex items-center justify-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-blue-100 dark:border-blue-900 shadow-sm hover:shadow-md transition group">
          <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/40 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <div class="text-left">
            <div class="text-[10px] uppercase tracking-wider font-bold text-blue-500">Syllabus</div>
            <div class="text-sm font-semibold dark:text-white">Sem <?= $semester ?> PDF</div>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($sem_res['timetable_link']): ?>
        <a href="<?= safe($sem_res['timetable_link']) ?>" target="_blank" 
           class="flex items-center justify-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-purple-100 dark:border-purple-900 shadow-sm hover:shadow-md transition group">
          <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/40 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </div>
          <div class="text-left">
            <div class="text-[10px] uppercase tracking-wider font-bold text-purple-500">Timetable</div>
            <div class="text-sm font-semibold dark:text-white">Sem <?= $semester ?> Schedule</div>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($sem_res['calendar_link']): ?>
        <a href="<?= safe($sem_res['calendar_link']) ?>" target="_blank" 
           class="flex items-center justify-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-indigo-100 dark:border-indigo-900 shadow-sm hover:shadow-md transition group">
          <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
          </div>
          <div class="text-left">
            <div class="text-[10px] uppercase tracking-wider font-bold text-indigo-500">Academic</div>
            <div class="text-sm font-semibold dark:text-white">Calendar 2024-25</div>
          </div>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
    </div>
  </div>

  <script>
    document.getElementById('courseSearch')?.addEventListener('input', function(e) {
      const term = e.target.value.toLowerCase();
      document.querySelectorAll('.course-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const code = card.dataset.code.toLowerCase();
        if (name.includes(term) || code.includes(term)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  </script>

  <?php if (empty($courses)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No courses found for this semester.
    </div>
  <?php else: ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">

    <?php foreach ($courses as $c): 
      $img = $c['image_path'] ?: $DEFAULT_IMG;
    ?>

      <div data-name="<?= safe($c['name']) ?>"
           data-code="<?= safe($c['subject_code']) ?>"
           class="course-card group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 relative overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="mb-5 flex-grow">
          <div class="flex justify-between items-start mb-2">
            <h3 class="text-xl font-extrabold dark:text-white leading-tight uppercase font-['Sora'] tracking-tight">
              <?= safe($c['name']) ?> 
            </h3>
          </div>
          <?php if($c['subject_code']): ?>
            <div class="inline-block bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 text-[11px] font-bold px-3 py-1 rounded-lg mb-3">
              <?= safe($c['subject_code']) ?>
            </div>
          <?php endif; ?>
          <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
            Comprehensive academic resources, notes, and previous year question papers for thorough preparation.
          </p>
        </div>

        <!-- Buttons Grid -->
        <div class="space-y-3 mt-auto">
          <!-- Row 1 -->
          <div class="grid grid-cols-2 gap-3">
            <a href="view_link.php?course_id=<?= $c['id'] ?>" class="flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 text-white text-[10px] sm:text-xs font-bold rounded-xl transition shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              QP & Answer Key
            </a>
            <a href="view_link.php?course_id=<?= $c['id'] ?>" class="flex items-center justify-center gap-2 py-3 bg-white dark:bg-gray-700 border border-blue-200 dark:border-gray-600 text-blue-600 dark:text-blue-400 text-[10px] sm:text-xs font-bold rounded-xl hover:bg-blue-50 dark:hover:bg-gray-600 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
              Modules
            </a>
          </div>

          <!-- Row 2 (Featured) -->
          <a href="#" class="flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-[10px] sm:text-xs font-bold rounded-xl transition shadow-md w-full">
            <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 11-2 0 1 1 0 012 0zM8 16v-1a1 1 0 10-2 0v1a1 1 0 102 0zM13.657 15.657a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM15 10a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
            Try with SnapLearn (Syllabus)
          </a>

          <!-- Row 3 -->
          <div class="grid grid-cols-2 gap-3">
            <a href="#" class="flex items-center justify-center gap-2 py-3 bg-green-800/10 dark:bg-green-900/40 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-[10px] sm:text-xs font-bold rounded-xl hover:bg-green-100 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              Try KLAW Notes
            </a>
            <div class="flex gap-1">
              <a href="view_link.php?course_id=<?= $c['id'] ?>#pyqs" class="flex-1 flex items-center justify-center gap-2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] sm:text-xs font-bold rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                PYQ
              </a>
              <a href="submit_pyq.php?course_id=<?= $c['id'] ?>" class="flex items-center justify-center w-10 py-3 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold rounded-xl hover:bg-indigo-200 transition" title="Add PYQ">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
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
