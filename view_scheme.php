<?php
include 'db.php';
if (!function_exists('safe')) {
    function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }
}

$schemes = $pdo->query('SELECT * FROM schemes ORDER BY name')->fetchAll();

// Verify signature for anti-scraping
if (!verify_url_sig()) {
    // Optional: Only log or redirect if accessed directly without signature
}
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
    <?php if (!empty($schemes)): foreach ($schemes as $s): 
      $img = 'assets/logooo.png'; // Default
      if (strpos(strtolower($s['name']), '2019') !== false) $img = 'assets/2019/1.jpg';
      if (strpos(strtolower($s['name']), '2024') !== false || strpos(strtolower($s['name']), '2025') !== false) $img = 'assets/2025/1.jpg';
    ?>
      <!-- FIX: Removed link to semesters. Now goes to branches -->
      <a href="<?= sign_url('view_branch.php', ['scheme_id' => $s['id']]) ?>"
         class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="aspect-video w-full overflow-hidden bg-gray-200 dark:bg-gray-700">
          <img src="<?= $img ?>" alt="<?= safe($s['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        <div class="p-6">
          <div class="text-2xl font-bold font-['Sora'] text-blue-600 dark:text-blue-400"><?= safe($s['name']) ?></div>
          <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-3">
            <span>View branches</span>
            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </div>
        </div>
      </a>
    <?php endforeach; else: ?>
      <div class="col-span-full text-center text-gray-500">No schemes found.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
