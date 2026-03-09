<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$schemes = $pdo->query('SELECT * FROM schemes ORDER BY name')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Schemes — KTU Magic</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Browse various KTU schemes and access academic resources for 2019, 2024, and more on KTU Magic.">
  <meta name="keywords" content="KTU Schemes, KTU 2019 Scheme, KTU 2024 Scheme, KTU Magic">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">
<?php include 'nav.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <h1 class="text-3xl font-bold font-['Sora']">Schemes</h1>
    
    <form action="search.php" method="GET" class="relative w-full md:w-96 group">
      <input type="text" name="q" placeholder="Search any course or code..." 
             class="w-full pl-10 pr-4 py-3 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all group-hover:shadow-md">
      <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <?php if (!empty($schemes)): foreach ($schemes as $s): ?>
      <!-- FIX: Removed link to semesters. Now goes to branches -->
      <a href="view_branch.php?scheme_id=<?= $s['id'] ?>"
         class="block bg-white dark:bg-gray-800 rounded-2xl p-6 shadow hover:shadow-lg transition">
        <div class="text-2xl font-semibold"><?= safe($s['name']) ?></div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">View branches</div>
      </a>
    <?php endforeach; else: ?>
      <div class="col-span-full text-center text-gray-500">No schemes found.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
