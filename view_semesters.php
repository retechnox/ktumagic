<?php
include 'db.php';

function safe($v)
{
  return htmlspecialchars((string)$v, ENT_QUOTES);
}

$branch_id = intval($_GET['branch_id'] ?? 0);
if (!$branch_id) {
  header("Location: view_scheme.php");
  exit;
}

// Verify signature for anti-scraping
if (!verify_url_sig()) {
  header("Location: 404.php");
  exit;
}

// Fetch branch
$bq = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$bq->execute([$branch_id]);
$branch = $bq->fetch();
// DEBUG
// error_log("Branch Fetch: " . print_r($branch, true));
if (!$branch) {
  header("Location: view_scheme.php");
  exit;
}

// Fetch parent scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$branch['scheme_id']]);
$scheme = $sq->fetch();

$branch_image = $branch['image_path'] ?:
  "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80";

$mode = isset($_GET['mode']) ? trim($_GET['mode']) : null;
$sem_data = json_decode($branch['semester_data'] ?: '{}', true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>
    <?= safe($branch['name'])?> — Semesters
  </title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description"
    content="Access semester-wise academic resources for <?= safe($branch['name'])?> on KTU Magic. Select your semester to find notes and courses.">
  <meta name="keywords" content="KTU Semesters, <?= safe($branch['name'])?> Semesters, KTU Magic">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap"
    rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900">
  <?php include 'nav.php'; ?>

  <main class="flex-grow">
    <div class="max-w-5xl mx-auto px-4 pb-10">

      <!-- Breadcrumb -->
      
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="<?= sign_url('view_scheme.php', [])?>" class="hover:underline">Schemes</a> &rsaquo;
    <a href="<?= sign_url('view_branch.php', ['scheme_id' => $scheme['id']])?>" class="hover:underline"><?= safe($scheme['name'])?></a> 
    &rsaquo;
    <span class="font-semibold"><?= safe($branch['name'])?></span>
  </div>
 

      <!-- Header banner -->
      <div class="relative rounded-[2rem] overflow-hidden shadow-2xl mb-12 group">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
        <?php if ($mode !== 'syllabus'): ?>
        <img referrerpolicy="no-referrer" src="<?= safe($branch_image)?>"
          class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-700"
          onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&q=80'">
        <?php endif; ?>

        <div class="absolute inset-0 flex flex-col items-center justify-center z-20 p-6 text-center">
          <span
            class="px-4 py-1 bg-blue-600/30 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full mb-4 tracking-widest uppercase">
            <?= safe($scheme['name'])?>
          </span>

        </div>
      </div>

      <div class="flex flex-col items-center justify-center text-center mb-10">
        <div class="w-20 h-1.5 bg-blue-600 rounded-full mb-6"></div>
        <p class="text-gray-500 dark:text-gray-400 max-w-lg">
          <?php
if ($mode)
  echo "Select a semester to directly view its " . safe($mode) . ".";
else
  echo "Access study materials, previous question papers, and essential notes curated for your specific semester.";
?>
        </p>
      </div>

      <!-- Semester Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php 
        $isGroup = stripos($branch['name'], 'group') !== false;
        $maxSem = $isGroup ? 2 : 8;
        for ($i = 1; $i <= $maxSem; $i++):
  $has_link = ($mode && isset($sem_data[$i][$mode]) && !empty($sem_data[$i][$mode]));

  if ($mode) {
    if ($has_link) {
      $target_link = $sem_data[$i][$mode];
      $is_external = true;
    }
    elseif (strcasecmp($mode, 'notes') === 0) {
      // For notes, if no direct link, fallback to courses list
      $target_branch_id = (($i === 1 || $i === 2) && isset($branch['redirect_branch_id']) && $branch['redirect_branch_id']) 
                          ? $branch['redirect_branch_id'] 
                          : $branch_id;
      $target_link = sign_url('view_courses.php', ['branch_id' => $target_branch_id, 'semester' => $i, 'mode' => $mode]);
      $is_external = false;
    }
    else {
      // Strict modes (calendar, timetable) -> 404 if missing
      $target_link = "404.php";
      $is_external = false;
    }
  }
  else {
    // Default: Courses
    $target_branch_id = (($i === 1 || $i === 2) && isset($branch['redirect_branch_id']) && $branch['redirect_branch_id']) 
                        ? $branch['redirect_branch_id'] 
                        : $branch_id;
    $target_link = sign_url('view_courses.php', ['branch_id' => $target_branch_id, 'semester' => $i, 'mode' => $mode]);
    $is_external = false;
  }
?>
        <a href="<?= $is_external ? safe($target_link) : $target_link?>" <?= $is_external ? 'target="_blank"' : '' ?>
          class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border
          border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 flex
          flex-col items-center justify-center text-center">

          <!-- Hover Background Glow -->
          <div
            class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
          </div>

          <div class="relative mb-4">
            <div
              class="absolute inset-0 bg-blue-600 blur-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-500">
            </div>
            <div
              class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-3 transition-all duration-500 relative z-10">
              <span class="text-2xl font-extrabold font-['Sora']">S
                <?= $i?>
              </span>
            </div>
          </div>

          <h3
            class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-0 group-hover:text-blue-600 transition-colors">
            Semester
            <?= $i?>
          </h3>
          <div
            class="mt-4 w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
            <?php if ($mode): ?>
            <?php if ($has_link): ?>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            <?php
    elseif (strcasecmp($mode, 'notes') === 0): ?>
            <!-- Normal arrow for notes fallback -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
            <?php
    else: ?>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
              </path>
            </svg>
            <?php
    endif; ?>
            <?php
  else: ?>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
            <?php
  endif; ?>
          </div>
        </a>
        <?php
endfor; ?>
      </div>

    </div>
  </main>

  <?php include 'footer.php'; ?>
</body>

</html>