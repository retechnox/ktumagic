<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$search = trim($_GET['q'] ?? '');
$scheme_id = intval($_GET['scheme_id'] ?? 0);

$params = [];
$sql = "SELECT c.*, b.name as branch_name, s.name as scheme_name 
        FROM courses c 
        JOIN branches b ON c.branch_id = b.id 
        JOIN schemes s ON c.scheme_id = s.id 
        WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (c.name LIKE ? OR c.subject_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($scheme_id > 0) {
    $sql .= " AND c.scheme_id = ?";
    $params[] = $scheme_id;
}

$sql .= " ORDER BY s.name DESC, b.name ASC, c.semester ASC, c.name ASC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$schemes = $pdo->query("SELECT * FROM schemes ORDER BY name")->fetchAll();

$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Courses — KTU Magic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        .search-container {
            background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">
    <?php include 'nav.php'; ?>

    <!-- Search Header -->
    <div class="search-container py-16 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6 font-['Sora']">Search Courses</h1>
            <p class="text-blue-100 text-lg mb-8">Find notes, question papers, and resources by subject name or code.</p>
            
            <form action="search.php" method="GET" class="relative max-w-2xl mx-auto">
                <input type="text" name="q" value="<?= safe($search) ?>" 
                       placeholder="Try 'Calculus' or 'MAT101'..." 
                       class="w-full pl-6 pr-32 py-4 rounded-2xl border-none shadow-2xl focus:ring-4 focus:ring-blue-400 outline-none text-lg text-gray-800">
                <button type="submit" class="absolute right-2 top-2 bottom-2 bg-blue-600 text-white px-6 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h2 class="text-2xl font-bold font-['Sora']">
                <?php if ($search !== ''): ?>
                    Results for "<?= safe($search) ?>"
                <?php else: ?>
                    Explore All Courses
                <?php endif; ?>
                <span class="text-gray-400 dark:text-gray-500 font-normal text-lg ml-2">(<?= count($results) ?> found)</span>
            </h2>

            <!-- Quick Filter -->
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <span class="text-sm font-semibold text-gray-500 px-2">Scheme:</span>
                <div class="flex gap-2">
                    <a href="search.php?q=<?= urlencode($search) ?>" 
                       class="px-3 py-1 rounded-lg text-sm <?= $scheme_id == 0 ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                       All
                    </a>
                    <?php foreach($schemes as $s): ?>
                        <a href="search.php?q=<?= urlencode($search) ?>&scheme_id=<?= $s['id'] ?>" 
                           class="px-3 py-1 rounded-lg text-sm <?= $scheme_id == $s['id'] ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                           <?= safe($s['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (empty($results)): ?>
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl shadow-inner border border-dashed border-gray-200 dark:border-gray-700">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-bold mb-2">No courses found</h3>
                <p class="text-gray-500 dark:text-gray-400">Try a different keyword or check the spelling.</p>
                <a href="search.php" class="mt-6 inline-block text-blue-600 font-bold hover:underline">View all courses →</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($results as $c): 
                    $img = $c['image_path'] ?: $DEFAULT_IMG;
                ?>
                    <a href="view_link.php?course_id=<?= $c['id'] ?>" 
                       class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        
                        <div class="relative h-48 overflow-hidden">
                            <img src="<?= safe($img) ?>" referrerpolicy="no-referrer" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 onerror="this.src='<?= $DEFAULT_IMG ?>'">
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md text-gray-900 dark:text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">
                                    S<?= safe($c['semester']) ?>
                                </span>
                                <?php if($c['subject_code']): ?>
                                    <span class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">
                                        <?= safe($c['subject_code']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
                                <?= safe($c['scheme_name']) ?> • <?= safe($c['branch_name']) ?>
                            </div>
                            <h3 class="text-xl font-bold mb-4 line-clamp-2 dark:text-white group-hover:text-blue-600 transition-colors">
                                <?= safe($c['name']) ?>
                            </h3>
                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">View Resources</span>
                                <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    →
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
