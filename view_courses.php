<?php
include 'db.php';

$branch_id = intval($_GET['branch_id'] ?? 0);
$semester = intval($_GET['semester'] ?? 0);

if (!$branch_id || !$semester) {
  header("Location: view_scheme.php");
  exit;
}

// Verify signature for anti-scraping
if (!verify_url_sig()) {
  header("Location: index.php");
  exit;
}

// Fetch branch
$bq = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$bq->execute([$branch_id]);
$branch = $bq->fetch();
if (!$branch) {
  header("Location: view_scheme.php");
  exit;
}

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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>
    <?= safe($branch['name'])?> — Sem
    <?= $semester?> | KTU Magic
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description"
    content="Browse courses and study materials for <?= safe($branch['name'])?> Semester <?= $semester?> on KTU Magic.">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap"
    rel="stylesheet">
  <style>
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #e2e8f0;
      border-radius: 10px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #334155;
    }
  </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen">
  <?php include 'nav.php'; ?>

  <main class="flex-grow">
    <div class="max-w-6xl mx-auto px-4 py-10">

      <!-- Breadcrumb -->
      <div class="text-sm text-gray-600 dark:text-gray-400 mb-8">
        <a href="<?= sign_url('view_scheme.php', [])?>" class="hover:underline">Schemes</a> &rsaquo;
        <a href="<?= sign_url('view_branch.php', ['scheme_id' => $scheme['id']])?>" class="hover:underline">
          <?= safe($scheme['name'])?>
        </a> &rsaquo;
        <a href="<?= sign_url('view_semesters.php', ['branch_id' => $branch_id])?>" class="hover:underline">
          <?= safe($branch['name'])?>
        </a> &rsaquo;
        <span class="font-semibold text-blue-600">Sem
          <?= $semester?>
        </span>
      </div>

      <div class="flex flex-col lg:flex-row justify-between items-end mb-2 gap-8">
        <div class="flex items-center gap-6">

          <div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white font-['Sora'] tracking-tight">
              <?= safe($branch['name'])?>
            </h1>
            <div class="flex items-center gap-3 mt-3">
              <span
                class="px-4 py-1.5 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold tracking-widest uppercase rounded-full border border-blue-200 dark:border-blue-800">Semester
                <?= $semester?>
              </span>
              <span class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                <?= count($courses)?> Courses Found
              </span>
            </div>
          </div>
        </div>

        <div class="relative w-full lg:w-96 group">
          <input type="text" id="courseSearch" placeholder="Search by name or subject code..."
            class="w-full pb-3 pl-14 pr-4 pt-3 py-4.5 rounded-[1.5rem] bg-white dark:bg-gray-800 border-2 border-transparent shadow-xl focus:border-blue-500 outline-none transition-all dark:text-white placeholder-gray-400">
          <div
            class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <br />




      <?php if (empty($courses)): ?>
      <div
        class="flex flex-col items-center justify-center py-24 bg-white dark:bg-gray-800 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div
          class="w-24 h-24 bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center text-gray-400 mb-8 border border-gray-100 dark:border-gray-800">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
            </path>
          </svg>
        </div>
        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3 font-['Sora']">No courses found</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-sm text-center">We haven't added resources for this semester
          yet. Check back soon!</p>
      </div>
      <?php
else: ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ($courses as $c):
    $img = $c['image_path'] ?: $DEFAULT_IMG;
    $links = json_decode($c['links'] ?? '[]', true) ?: [];
    $pyqs = json_decode($c['pyqs'] ?? '[]', true) ?: [];
    $modules = json_decode($c['modules'] ?? '[]', true) ?: [];
    $qp_answers = json_decode($c['qp_answers'] ?? '[]', true) ?: [];

    $hasPyq = !empty($pyqs);
    $hasModules = !empty($modules);
    $hasQp = !empty($qp_answers);
    $hasOther = !empty($links);

    $canModules = $hasModules || $hasOther;
?>
        <div data-name="<?= safe($c['name'])?>" data-code="<?= safe($c['subject_code'])?>"
          class="course-card group relative bg-white dark:bg-gray-800 rounded-3xl shadow-md border-2 border-gray-300 dark:border-gray-600 hover:shadow-2xl hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 flex flex-col overflow-hidden">

          <div class="p-6 pb-6 flex-grow text-center">
            <?php if ($c['subject_code']): ?>
            <span
              class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-bold uppercase tracking-widest rounded-lg mb-2">
              <?= safe($c['subject_code'])?>
            </span>
            <?php
    endif; ?>

            <div class="flex items-center justify-center">
              <h3
                class="text-xl font-bold text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 transition-colors font-['Sora'] line-clamp-4 min-h-[3.5rem]">
                <?= safe($c['name'])?>
              </h3>
            </div>
          </div>

          <!-- Buttons Group - 80% width and centered -->
          <div class="px-6 pb-8">
            <div
              class="w-[90%] mx-auto overflow-hidden rounded-2xl border-2 border-gray-200 dark:border-gray-700 divide-y-2 divide-gray-200 dark:divide-gray-700 shadow-sm">
              <!-- Main Action: Study Modules -->
              <button onclick='showDrawer("<?= $c[' id']?>", "modules")'
                class="flex items-center justify-center gap-3 w-full py-4 px-4 text-sm font-bold uppercase
                tracking-wider transition-all
                <?= $canModules ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-50 dark:bg-gray-800/50 text-gray-400 cursor-not-allowed'?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                  </path>
                </svg>
                Module Notes
              </button>

              <!-- Secondary Actions: Answers & PYQ -->
              <div class="grid grid-cols-2 divide-x-2 divide-gray-200 dark:divide-gray-700">
                <button onclick='showDrawer("<?= $c[' id']?>", "qp")'
                  class="flex flex-col items-center justify-center py-3 px-2 bg-white dark:bg-gray-800 hover:bg-gray-50
                  dark:hover:bg-gray-700/50 transition-all
                  <?= $hasQp ? 'text-gray-700 dark:text-gray-200 font-bold' : 'text-gray-400 cursor-not-allowed'?>">
                  <span class="text-[10px] uppercase tracking-widest">Answers</span>
                  <span class="text-[9px] font-medium opacity-60">
                    <?= $hasQp ? 'Available' : 'Empty'?>
                  </span>
                </button>

                <button onclick='showDrawer("<?= $c[' id']?>", "pyq")'
                  class="flex flex-col items-center justify-center py-3 px-2 bg-white dark:bg-gray-800 hover:bg-gray-50
                  dark:hover:bg-gray-700/50 transition-all
                  <?= $hasPyq ? 'text-gray-700 dark:text-gray-200 font-bold' : 'text-gray-400 cursor-not-allowed'?>">
                  <span class="text-[10px] uppercase tracking-widest">PYQ</span>
                  <span class="text-[9px] font-medium opacity-60">
                    <?= $hasPyq ? 'Questions' : 'Empty'?>
                  </span>
                </button>
              </div>

              <!-- Footer Action: Contribute -->
              <a href="<?= sign_url('submit_material.php', ['course_id' => $c['id']])?>"
                class="flex items-center justify-center gap-2 py-3 bg-gray-50 dark:bg-gray-900/40 text-[10px] font-bold text-gray-500 dark:text-gray-400 hover:text-blue-600 hover:bg-white dark:hover:bg-gray-800 transition-all uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Contribute
              </a>
            </div>
          </div>

          <!-- Resource Drawer (Slide-up menu) -->
          <div id="drawer-<?= $c['id']?>"
            class="resource-drawer pointer-events-none opacity-0 translate-y-full absolute inset-0 bg-white dark:bg-gray-900 z-30 flex flex-col p-8 transition-all duration-500 rounded-[3rem]">
            <div class="flex justify-between items-center mb-10">
              <div class="space-y-1">
                <p class="text-[11px] font-black text-blue-500 uppercase tracking-[0.3em]">Resource List</p>
                <h4 id="drawer-title-<?= $c['id']?>"
                  class="text-2xl font-black text-gray-900 dark:text-white uppercase font-['Sora'] tracking-tight">
                  Resources</h4>
              </div>
              <button onclick='hideDrawer("<?= $c[' id']?>")' class="w-14 h-14 flex items-center justify-center
                bg-gray-50 dark:bg-gray-800 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20
                rounded-[1.25rem] transition-all">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12">
                  </path>
                </svg>
              </button>
            </div>

            <div id="drawer-content-<?= $c['id']?>" class="flex-grow space-y-4 overflow-y-auto pr-2 custom-scrollbar">
              <!-- Links injected via JS -->
            </div>

            <div class="mt-8 pt-8 border-t dark:border-gray-800 flex justify-center">
              <a href="<?= sign_url('view_link.php', ['course_id' => $c['id']])?>"
                class="flex items-center gap-3 text-[11px] font-black text-gray-400 hover:text-blue-500 transition-all uppercase tracking-[0.2em] group/link">
                View Full Details
                <svg class="w-4 h-4 group-hover/link:translate-x-2 transition-transform" fill="none"
                  stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3">
                  </path>
                </svg>
              </a>
            </div>
          </div>

        </div>

        <!-- Data for this course -->
        <script>
          window.courseData = window.courseData || {};
          window.courseData["<?= $c['id']?>"] = {
            qp: <?= json_encode($qp_answers) ?>,
            modules: <?= json_encode($modules) ?>,
            pyq: <?= json_encode($pyqs) ?>,
            other: <?= json_encode($links) ?>
        };
        </script>

        <?php
  endforeach; ?>
      </div>

      <!-- Semester Resources Section -->
      <?php
  $has_sem_links = $sem_res && ($sem_res['syllabus_link'] || $sem_res['timetable_link'] || $sem_res['calendar_link']);
  if ($has_sem_links): ?>
      <div class="mt-16 pt-8 border-t-2 border-gray-100 dark:border-gray-800">
        <h2
          class="text-base md:text-xl font-bold text-gray-900 dark:text-white mb-6 font-['Sora'] tracking-tight flex items-center gap-3">
          <span class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </span>
          Additional Resources
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <?php if ($sem_res['syllabus_link']): ?>
          <a href="<?= safe($sem_res['syllabus_link'])?>" target="_blank"
            class="flex items-center gap-4 p-4 md:p-6 bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-blue-500 transition-all group">
            <div
              class="w-10 h-10 md:w-14 md:h-14 bg-blue-50 dark:bg-blue-900/30 rounded-lg md:rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                </path>
              </svg>
            </div>
            <div>
              <p class="text-[9px] uppercase tracking-widest font-bold text-gray-400 mb-0.5">Resource</p>
              <h4 class="text-sm md:text-lg font-bold text-gray-800 dark:text-white leading-tight">Semester Syllabus
              </h4>
            </div>
          </a>
          <?php
    endif; ?>

          <?php if ($sem_res['timetable_link']): ?>
          <a href="<?= safe($sem_res['timetable_link'])?>" target="_blank"
            class="flex items-center gap-4 p-4 md:p-6 bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-purple-500 transition-all group">
            <div
              class="w-10 h-10 md:w-14 md:h-14 bg-purple-50 dark:bg-purple-900/30 rounded-lg md:rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <p class="text-[9px] uppercase tracking-widest font-bold text-gray-400 mb-0.5">Schedule</p>
              <h4 class="text-sm md:text-lg font-bold text-gray-800 dark:text-white leading-tight">Exam Timetable</h4>
            </div>
          </a>
          <?php
    endif; ?>

          <?php if ($sem_res['calendar_link']): ?>
          <a href="<?= safe($sem_res['calendar_link'])?>" target="_blank"
            class="flex items-center gap-4 p-4 md:p-6 bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-indigo-500 transition-all group">
            <div
              class="w-10 h-10 md:w-14 md:h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg md:rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
              </svg>
            </div>
            <div>
              <p class="text-[9px] uppercase tracking-widest font-bold text-gray-400 mb-0.5">Official</p>
              <h4 class="text-sm md:text-lg font-bold text-gray-800 dark:text-white leading-tight">Academic Calendar
              </h4>
            </div>
          </a>
          <?php
    endif; ?>
        </div>
      </div>
      <?php
  endif; ?>

      <style>
        .resource-drawer.active {
          pointer-events: auto !important;
          opacity: 1 !important;
          transform: translateY(0) !important;
        }
      </style>

      <script>
        function toPreview(url) {
          if (!url) return '#';
          if (url.includes('drive.google.com')) {
            return url.replace(/\/view(\?.*)?$/, '/preview');
          }
          return url;
        }

        function showDrawer(courseId, category) {
          const drawer = document.getElementById(`drawer-${courseId}`);
          const content = document.getElementById(`drawer-content-${courseId}`);
          const title = document.getElementById(`drawer-title-${courseId}`);
          const data = window.courseData[courseId];

          let displayLinks = [];
          let categoryTitle = '';

          if (category === 'pyq') {
            displayLinks = data.pyq || [];
            categoryTitle = 'Question Papers';
          } else if (category === 'qp') {
            displayLinks = data.qp || [];
            categoryTitle = 'Answer Keys';
          } else if (category === 'modules') {
            // Merge modules and other resources for a complete view
            displayLinks = [...(data.modules || []), ...(data.other || [])];
            categoryTitle = 'Study Materials';
          } else {
            displayLinks = data.other || [];
            categoryTitle = 'Resources';
          }

          title.innerText = categoryTitle;
          content.innerHTML = '';

          const uniqueLinks = [];
          const seenUrls = new Set();
          displayLinks.forEach(item => {
            if (!seenUrls.has(item.url)) {
              uniqueLinks.push(item);
              seenUrls.add(item.url);
            }
          });

          if (uniqueLinks.length === 0) {
            content.innerHTML = `<div class="py-20 text-center"><div class="text-gray-200 dark:text-gray-800 mb-4"><svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg></div><p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No entries found yet.</p></div>`;
          } else {
            uniqueLinks.forEach(item => {
              const previewUrl = toPreview(item.url);
              const signedUrl = `viewer_embed.php?url=${encodeURIComponent(previewUrl)}`;

              content.innerHTML += `
                    <a href="${signedUrl}" target="_blank" class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-800/50 rounded-[1.5rem] border-2 border-transparent hover:border-blue-500 hover:bg-white dark:hover:bg-gray-800 transition-all group/item shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm transition-transform group-hover/item:rotate-12">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="text-left">
                               <p class="text-sm font-black text-gray-800 dark:text-gray-100 group-hover/item:text-blue-600 transition-colors line-clamp-1">${item.link_name}</p>
                               <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Click to open doc</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-400 group-hover/item:bg-blue-600 group-hover/item:text-white transition-all">
                           <svg class="w-5 h-5 group-hover/item:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                `;
            });
          }

          drawer.classList.add('active');
        }

        function hideDrawer(courseId) {
          document.getElementById(`drawer-${courseId}`).classList.remove('active');
        }

        document.getElementById('courseSearch')?.addEventListener('input', function (e) {
          const term = e.target.value.toLowerCase();
          document.querySelectorAll('.course-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const code = card.dataset.code.toLowerCase();
            if (name.includes(term) || code.includes(term)) {
              card.style.display = 'flex';
            } else {
              card.style.display = 'none';
            }
          });
        });
      </script>

      <?php
endif; ?>

    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>

</html>