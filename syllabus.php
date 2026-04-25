<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$search = $_GET['search'] ?? '';
$scheme_filter = $_GET['scheme_id'] ?? '';
$branch_filter = $_GET['branch_id'] ?? '';

$params = [];
$sql = "SELECT c.*, b.name as branch_name, s.name as scheme_name 
        FROM courses c 
        JOIN branches b ON c.branch_id = b.id 
        JOIN schemes s ON c.scheme_id = s.id 
        WHERE (JSON_LENGTH(c.syllabus) > 0)";

if ($search) {
    $sql .= " AND c.name LIKE ?";
    $params[] = "%$search%";
}
if ($scheme_filter) {
    $sql .= " AND c.scheme_id = ?";
    $params[] = $scheme_filter;
}
if ($branch_filter) {
    $sql .= " AND (c.branch_id = ? OR c.branch_id = (SELECT redirect_branch_id FROM branches WHERE id = ? AND redirect_branch_id IS NOT NULL))";
    $params[] = $branch_filter;
    $params[] = $branch_filter;
}

$sql .= " ORDER BY c.semester ASC, c.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$schemes = $pdo->query("SELECT * FROM schemes ORDER BY name")->fetchAll();

$branch_sql = "SELECT * FROM branches";
$branch_params = [];
if ($scheme_filter) {
    $branch_sql .= " WHERE scheme_id = ?";
    $branch_params[] = $scheme_filter;
}
$branch_sql .= " ORDER BY (name LIKE '%Group%') DESC, name ASC";
$bz = $pdo->prepare($branch_sql);
$bz->execute($branch_params);
$branches = $bz->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus Search — KTU Magic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'nav.php'; ?>

    <div class="max-w-6xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-extrabold mb-8 text-center bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-blue-600">
            Course Syllabuses
        </h1>

        <form method="GET" id="filterForm" class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl mb-12 flex flex-wrap gap-6 items-end justify-center">
            
            <div class="w-full md:w-64">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Search Course</label>
                <input type="text" name="search" id="searchInput" value="<?= safe($search) ?>" placeholder="Enter course name..." 
                       class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all focus:border-green-500 text-sm">
            </div>
            
            <div class="w-full md:w-auto">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Scheme</label>
                <select name="scheme_id" id="scheme_select" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all focus:border-green-500 text-sm">
                    <option value="">All Schemes</option>
                    <?php foreach($schemes as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $scheme_filter == $s['id'] ? 'selected' : '' ?>><?= safe($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <select name="branch_id" id="branch_select" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all focus:border-green-500 text-sm">
                    <option value="">All Branches</option>
                    <?php foreach($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" data-scheme="<?= $b['scheme_id'] ?>" <?= $branch_filter == $b['id'] ? 'selected' : '' ?>><?= safe($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (empty($courses)): ?>
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl shadow-inner border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400 text-xl">No syllabuses found matching your criteria. 📚</p>
                <a href="syllabus.php" class="text-green-600 mt-4 inline-block hover:underline font-bold">Clear all filters</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($courses as $c): ?>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition transform hover:-translate-y-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight"><?= safe($c['name']) ?></h3>
                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                S<?= $c['semester'] ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"><?= safe($c['scheme_name']) ?> — <?= safe($c['branch_name']) ?></p>
                        
                        <div class="mt-auto">
                            <a href="<?= sign_url('view_syllabus.php', ['course_id' => $c['id']]) ?>" 
                               class="w-full inline-flex items-center justify-center gap-3 py-4 bg-green-600 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-widest hover:bg-green-700 transition shadow-lg shadow-green-500/20 group/btn">
                                View Syllabus
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schemeSelect = document.getElementById('scheme_select');
            const branchSelect = document.getElementById('branch_select');
            const branchOptions = Array.from(branchSelect.options);

            function filterBranches(resetSelection = false) {
                const selectedScheme = schemeSelect.value;
                let firstVisible = null;
                let currentValid = false;

                branchOptions.forEach(option => {
                    if (option.value === "") {
                        option.hidden = false;
                        return;
                    }
                    const branchScheme = option.getAttribute('data-scheme');
                    if (selectedScheme === "" || branchScheme === selectedScheme) {
                        option.hidden = false;
                        if (!firstVisible) firstVisible = option.value;
                        if (option.value === branchSelect.value) currentValid = true;
                    } else {
                        option.hidden = true;
                    }
                });

                if (resetSelection && !currentValid) {
                    branchSelect.value = "";
                }
            }

            schemeSelect.addEventListener('change', () => filterBranches(true));
            filterBranches(false);

            // Debounce for search
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterForm.submit();
                }, 500); // 500ms debounce
            });
        });
    </script>
</body>
</html>
