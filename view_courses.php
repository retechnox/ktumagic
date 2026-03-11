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
      $links = json_decode($c['links'] ?? '[]', true) ?: [];
      $pyqs = json_decode($c['pyqs'] ?? '[]', true) ?: [];
      $modules = json_decode($c['modules'] ?? '[]', true) ?: [];
      $qp_answers = json_decode($c['qp_answers'] ?? '[]', true) ?: [];
      $other_links = json_decode($c['links'] ?? '[]', true) ?: [];
      
      $hasPyq = !empty($pyqs);
      $hasModules = !empty($modules);
      $hasQp = !empty($qp_answers);
      $hasOther = !empty($other_links);
      
      $canModules = $hasModules || $hasOther;
    ?>
      <div data-name="<?= safe($c['name']) ?>"
           data-code="<?= safe($c['subject_code']) ?>"
           class="course-card group bg-white dark:bg-gray-800 rounded-[2rem] p-6 sm:p-8 border-2 border-blue-500/30 dark:border-blue-500/40 hover:border-blue-500 transition-all duration-300 relative flex flex-col text-center overflow-hidden shadow-sm hover:shadow-xl">
        
        <!-- Header -->
        <div class="mb-6 flex-grow">
          <h3 class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 leading-tight uppercase font-['Sora'] tracking-tight mb-3">
            <?= safe($c['name']) ?> 
          </h3>
          <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium leading-relaxed max-w-xs mx-auto">
            Access curated notes, previous papers, and study materials.
          </p>
        </div>

        <!-- Buttons Grid -->
        <div class="space-y-4 mt-auto relative z-10">
          <!-- Main Action: Modules (Biggest & Boldest) -->
          <button onclick='showDrawer("<?= $c['id'] ?>", "modules")'
             class="flex items-center justify-center gap-3 py-4 <?= $canModules ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' ?> text-white text-sm sm:text-base font-black rounded-2xl transition-all duration-300 shadow-xl shadow-blue-500/25 uppercase tracking-wider w-full transform group-hover:scale-[1.02]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Study Modules
          </button>

          <!-- Secondary Actions -->
          <div class="grid grid-cols-2 gap-3">
            <button onclick='showDrawer("<?= $c['id'] ?>", "qp")'
               class="flex items-center justify-center gap-2 py-3 <?= $hasQp ? 'bg-white dark:bg-gray-900 border-2 border-blue-100 dark:border-blue-900/50 text-gray-700 dark:text-gray-300' : 'bg-gray-50 dark:bg-gray-900 text-gray-400 border-2 border-gray-100 dark:border-gray-800' ?> text-[10px] sm:text-xs font-bold rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-200 dark:hover:border-blue-800 transition uppercase tracking-widest">
               QP & Answers
            </button>
            
            <button onclick='showDrawer("<?= $c['id'] ?>", "pyq")'
               class="flex items-center justify-center gap-2 py-3 <?= $hasPyq ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-2 border-indigo-100 dark:border-indigo-900/50' : 'bg-gray-50 dark:bg-gray-900 text-gray-400 border-2 border-gray-100 dark:border-gray-800' ?> text-[10px] sm:text-xs font-bold rounded-xl hover:bg-white dark:hover:bg-gray-800 transition uppercase tracking-widest">
               <?= $hasPyq ? 'PYQ' : 'No PYQ' ?>
            </button>
          </div>

          <!-- Add Content Link -->
          <a href="submit_material.php?course_id=<?= $c['id'] ?>" 
             class="flex items-center justify-center gap-2 py-2 text-[10px] font-bold text-gray-400 hover:text-blue-500 transition uppercase tracking-[0.2em]">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Contribute Material
          </a>
        </div>

        <!-- Resource Drawer (The "Extra Layer") -->
        <div id="drawer-<?= $c['id'] ?>" class="resource-drawer pointer-events-none opacity-0 translate-y-full absolute inset-0 bg-white dark:bg-gray-800 z-20 flex flex-col p-6 transition-all duration-500 rounded-[2rem]">
            <div class="flex justify-between items-center mb-6">
                <h4 id="drawer-title-<?= $c['id'] ?>" class="text-xl font-black text-blue-600 dark:text-blue-400 uppercase font-['Sora'] tracking-tight">Resources</h4>
                <button onclick='hideDrawer("<?= $c['id'] ?>")' class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="drawer-content-<?= $c['id'] ?>" class="flex-grow space-y-3 overflow-y-auto pr-2">
                <!-- Links will be injected here via JS -->
            </div>

            <div class="mt-6 pt-4 border-t dark:border-gray-700 flex justify-center">
                <a href="view_link.php?course_id=<?= $c['id'] ?>" class="text-xs font-bold text-gray-400 hover:text-blue-500 transition uppercase tracking-widest">View All Details →</a>
            </div>
        </div>

      </div>

      <!-- Data for this course -->
      <script>
        window.courseData = window.courseData || {};
        window.courseData["<?= $c['id'] ?>"] = {
            qp: <?= json_encode($qp_answers) ?>,
            modules: <?= json_encode($modules) ?>,
            pyq: <?= json_encode($pyqs) ?>,
            other: <?= json_encode($links) ?>
        };
      </script>

    <?php endforeach; ?>
  </div>

  <style>
    .resource-drawer.active {
        pointer-events: auto;
        opacity: 1;
        translate-y: 0;
        transform: translateY(0);
    }
  </style>

  <script>
    function toPreview(url) {
        if (!url) return '#';
        // Simple conversion for Drive links to ensure they embed properly
        return url.replace(/\/view(\?.*)?$/, '/preview');
    }

    function showDrawer(courseId, category) {
        const drawer = document.getElementById(`drawer-${courseId}`);
        const content = document.getElementById(`drawer-content-${courseId}`);
        const title = document.getElementById(`drawer-title-${courseId}`);
        const data = window.courseData[courseId];
        
        let displayLinks = [];
        let categoryTitle = '';

        if (category === 'pyq') {
            displayLinks = data.pyq;
            categoryTitle = 'Previous Year Questions';
        } else if (category === 'qp') {
            displayLinks = data.qp;
            categoryTitle = 'QP & Answer Keys';
        } else if (category === 'modules') {
            displayLinks = data.modules;
            categoryTitle = 'Subject Modules';

            // Fallback for modules: if empty, show general resources
            if (displayLinks.length === 0 && data.other.length > 0) {
                displayLinks = data.other;
                categoryTitle = 'Study Resources';
            }
        } else {
            displayLinks = data.other;
            categoryTitle = 'General Resources';
        }

        title.innerText = categoryTitle;
        content.innerHTML = '';
        
        // Remove duplicates if any
        const uniqueLinks = [];
        const seenUrls = new Set();
        displayLinks.forEach(item => {
            if (!seenUrls.has(item.url)) {
                uniqueLinks.push(item);
                seenUrls.add(item.url);
            }
        });

        if (uniqueLinks.length === 0) {
            content.innerHTML = `<div class="py-10 text-center"><p class="text-gray-400 font-medium">No resources found in this category.</p></div>`;
        } else {
            uniqueLinks.forEach(item => {
                const previewUrl = toPreview(item.url);
                const embedUrl = `viewer_embed.php?url=${encodeURIComponent(previewUrl)}`;
                
                content.innerHTML += `
                    <a href="${embedUrl}" target="_blank" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400 transition group/item">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover/item:text-blue-600 transition truncate max-w-[200px]">${item.link_name}</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover/item:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                `;
            });
        }

        drawer.classList.add('active');
    }

    function hideDrawer(courseId) {
        document.getElementById(`drawer-${courseId}`).classList.remove('active');
    }
  </script>


  </div>

  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
