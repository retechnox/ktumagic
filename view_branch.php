<?php
include 'db.php';

if (!function_exists('safe')) {
    function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }
}

$scheme_id = intval($_GET['scheme_id'] ?? 0);
$mode = $_GET['mode'] ?? '';
if (!$scheme_id) { header("Location: view_scheme.php"); exit; }

// Verify signature for anti-scraping
if (!verify_url_sig()) {
    header("Location: index.php"); 
    exit;
}

// Fetch scheme
$sq = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$sq->execute([$scheme_id]);
$scheme = $sq->fetch();
if (!$scheme) { header("Location: view_scheme.php"); exit; }

// Fetch branches for this scheme
$checkOrder = $pdo->query("SHOW COLUMNS FROM branches LIKE 'display_order'")->rowCount() > 0;
$orderBy = $checkOrder ? "(display_order = 0 OR display_order IS NULL) ASC, display_order ASC, name ASC" : "name ASC";
$stmt = $pdo->prepare("SELECT * FROM branches WHERE scheme_id = ? ORDER BY $orderBy");
$stmt->execute([$scheme_id]);
$branches = $stmt->fetchAll();

$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= safe($scheme['name']) ?> — Branches</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="View various branches available for <?= safe($scheme['name']) ?> on KTU Magic. Access academic resources and notes for your specific branch.">
<meta name="keywords" content="KTU Branches, <?= safe($scheme['name']) ?> Branches, KTU Magic">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">

  <!-- Breadcrumb -->
  <!--
  <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
    <a href="<?= sign_url('view_scheme.php', []) ?>" class="hover:underline">Schemes</a> &rsaquo;
    <span class="font-semibold"><?= safe($scheme['name']) ?></span>
  </div>
  -->

  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <h1 class="text-2xl font-bold font-['Sora'] dark:text-white">
      Branches — <?= safe($scheme['name']) ?>
    </h1>
    
    <form action="search.php" method="GET" class="relative w-full md:w-96 group">
      <input type="hidden" name="scheme_id" value="<?= $scheme['id'] ?>">
      <input type="text" name="q" placeholder="Search in <?= safe($scheme['name']) ?>..." 
             class="w-full pl-10 pr-4 py-3 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all group-hover:shadow-md">
      <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      </div>
    </form>
  </div>

  <?php if (empty($branches)): ?>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow text-gray-600 dark:text-gray-300">
      No branches found for this scheme.
    </div>

  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

      <?php 
        foreach ($branches as $b): 
          $img = $b['image_path'] ?: $DEFAULT_IMG;
          $displayName = $b['name'];
      ?>

      <!-- FIXED FLOW: Branch now goes to semesters based on BRANCH -->
      <a href="<?= sign_url('view_semesters.php', ['branch_id' => $b['id'], 'mode' => $mode]) ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-xl transition">

        <?php if ($mode !== 'syllabus'): ?>
        <img referrerpolicy="no-referrer" src="<?= safe($img) ?>"
             class="w-full aspect-[4/3] object-cover rounded-lg mb-3"
             onerror="this.src='<?= $DEFAULT_IMG ?>'">
        <?php endif; ?>

        <div class="text-lg font-semibold dark:text-white">
          <?= safe($displayName) ?>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          View semesters →
        </div>
      </a>

      <?php endforeach; ?>

    </div>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
