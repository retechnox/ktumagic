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
    <h1 class="text-2xl font-bold dark:text-white">
      Courses — <?= safe($branch['name']) ?> (Sem <?= $semester ?>)
    </h1>
    
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

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

    <?php foreach ($courses as $c): 
      $img = $c['image_path'] ?: $DEFAULT_IMG;
    ?>

      <a href="view_link.php?course_id=<?= $c['id'] ?>"
         data-name="<?= safe($c['name']) ?>"
         data-code="<?= safe($c['subject_code']) ?>"
         class="course-card block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-xl transition transform hover:-translate-y-1">

        <img   referrerpolicy="no-referrer" src="<?= safe($img) ?>"
             class="w-full h-36 object-cover rounded-lg mb-3"
             onerror="this.src='<?= $DEFAULT_IMG ?>'">

        <div class="flex justify-between items-start gap-2">
          <div class="text-lg font-semibold dark:text-white line-clamp-2">
            <?= safe($c['name']) ?>
          </div>
          <?php if($c['subject_code']): ?>
            <span class="shrink-0 bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-blue-900/40 dark:text-blue-300">
              <?= safe($c['subject_code']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
          <span>Open resources</span>
          <span class="text-blue-600">→</span>
        </div>

      </a>

    <?php endforeach; ?>

  </div>

  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
