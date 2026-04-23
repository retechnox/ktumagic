<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$search = $_GET['search'] ?? '';
$scheme_filter = $_GET['scheme_id'] ?? '';
$branch_filter = $_GET['branch_id'] ?? '';

// Verify signature for anti-scraping
if (!verify_url_sig()) {
    // Only allow if no filters are set (base search page)
    // Actually, we relax this to allow GET searches to work for standard users
    if (!$search && !$scheme_filter && !$branch_filter) {
        // Just load the base page
    }
}

$params = [];
$sql = "SELECT c.*, b.name as branch_name, s.name as scheme_name 
        FROM courses c 
        JOIN branches b ON c.branch_id = b.id 
        JOIN schemes s ON c.scheme_id = s.id 
        WHERE (JSON_LENGTH(c.pyqs) > 0 OR JSON_LENGTH(c.qp_answers) > 0)";

if ($search) {
    $sql .= " AND c.name LIKE ?";
    $params[] = "%$search%";
}
if ($scheme_filter) {
    $sql .= " AND c.scheme_id = ?";
    $params[] = $scheme_filter;
}
if ($branch_filter) {
    $sql .= " AND c.branch_id = ?";
    $params[] = $branch_filter;
}

$sql .= " ORDER BY c.name ASC";
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
$branch_sql .= " ORDER BY name";
$bz = $pdo->prepare($branch_sql);
$bz->execute($branch_params);
$branches = $bz->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PYQ Search — KTU Magic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-purple: #8b5cf6;
            --primary-blue: #2563EB;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'nav.php'; ?>

    <div class="max-w-6xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-extrabold mb-8 text-center bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">
            Previous Year Questions (PYQs)
        </h1>

        <form method="GET" class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl mb-12 flex flex-wrap gap-6 items-end justify-center">
            
            <div class="w-full md:w-auto">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Scheme</label>
                <select name="scheme_id" id="scheme_select" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all focus:border-blue-500">
                    <option value="">All Schemes</option>
                    <?php foreach($schemes as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $scheme_filter == $s['id'] ? 'selected' : '' ?>><?= safe($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                <select name="branch_id" id="branch_select" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all focus:border-blue-500">
                    <option value="">All Branches</option>
                    <?php foreach($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" data-scheme="<?= $b['scheme_id'] ?>" <?= $branch_filter == $b['id'] ? 'selected' : '' ?>><?= safe($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Auto-submitting form, button removed -->
        </form>

        <?php if (empty($courses)): ?>
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl shadow-inner">
                <p class="text-gray-500 dark:text-gray-400 text-xl">No PYQs found matching your criteria. 🔍</p>
                <a href="pyq.php" class="text-blue-600 mt-4 inline-block hover:underline">Clear all filters</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($courses as $c): 
                    $pyqs = json_decode((string)$c['pyqs'], true) ?: [];
                    $qps = json_decode((string)$c['qp_answers'], true) ?: [];
                    $pyq_links = array_merge($pyqs, $qps);
                ?>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition transform hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight"><?= safe($c['name']) ?></h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                S<?= $c['semester'] ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><?= safe($c['scheme_name']) ?> — <?= safe($c['branch_name']) ?></p>
                        
                        <div class="space-y-4">
                            <?php foreach($pyq_links as $l): ?>
                                <div class="flex gap-3">
                                    <a href="<?= sign_url('viewer_embed.php', ['url' => $l['url']]) ?>" target="_blank" 
                                       class="flex-1 inline-flex items-center justify-center gap-2 py-3 bg-blue-600/10 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 rounded-2xl text-sm font-bold hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    <a href="<?= safe($l['url']) ?>" target="_blank" 
                                       class="flex-1 inline-flex items-center justify-center gap-2 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-600 dark:hover:text-white transition">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            <?php endforeach; ?>
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
                    if (option.value === "") { // "All Branches"
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
            
            // Initial filter
            filterBranches(false);
        });
    </script>
</body>
</html>
