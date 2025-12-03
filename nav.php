<?php
// nav.php - common navbar (root)
?>
<nav class="bg-white dark:bg-gray-800 shadow-md p-4 mb-6">
  <div class="max-w-6xl mx-auto flex items-center justify-between">
    <a href="view_scheme.php" class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold">KT</div>
      <div>
        <div class="text-lg font-bold dark:text-white">KTU Magic</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">Schemes • Semesters • Branches • Courses</div>
      </div>
    </a>

    <div class="flex items-center gap-3">
      <a href="view_scheme.php" class="text-sm text-gray-700 dark:text-gray-200 hover:underline">Schemes</a>
      <button id="themeToggle" class="px-3 py-1 rounded-lg border dark:border-gray-600 text-sm dark:text-gray-300">🌙 / ☀️</button>
    </div>
  </div>
</nav>

<script>
(function(){
  const btn = document.getElementById('themeToggle');
  btn && btn.addEventListener('click', ()=>{
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
  });
  if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
})();
</script>
