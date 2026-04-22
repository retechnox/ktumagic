<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$course_id = intval($_GET['course_id'] ?? 0);
if (!$course_id) { header("Location: index.php"); exit; }

// Verify signature for anti-scraping
if (!verify_url_sig()) {
    header("Location: index.php");
    exit;
}

$cq = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cq->execute([$course_id]);
$course = $cq->fetch();
if (!$course) { header("Location: index.php"); exit; }

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();
    $link_name = trim($_POST['link_name'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $contributor = trim($_POST['contributor_name'] ?? '');
    $material_type = $_POST['material_type'] ?? 'pyq';

    if (empty($link_name) || empty($url)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pyq_submissions (course_id, link_name, url, material_type, contributor_name) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$course_id, $link_name, $url, $material_type, $contributor])) {
            $success = true;
        } else {
            $error = "Something went wrong. Please try again.";
        }
        }
    } catch (Exception $e) {
        $error = "Security Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Resource — <?= safe($course['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 font-['Inter']">
    <?php include 'nav.php'; ?>
    <div class="max-w-xl mx-auto px-4 py-32">
        <a href="<?= sign_url('view_courses.php', ['branch_id' => $course['branch_id'], 'semester' => $course['semester']]) ?>" class="text-blue-600 dark:text-blue-400 hover:underline mb-8 inline-flex items-center gap-2 font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Courses
        </a>
        
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full -mr-16 -mt-16"></div>
            
            <h1 class="text-3xl font-black mb-2 font-['Sora'] tracking-tight">Support Community</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-8 font-medium">
                You are contributing resources for:
                <span class="block mt-2 px-6 py-3 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-2xl border border-blue-100 dark:border-blue-800 font-black text-xl uppercase tracking-tight">
                    <?= safe($course['name']) ?>
                    <?php if($course['subject_code']): ?>
                        <span class="block text-xs opacity-60 font-bold mt-1"><?= safe($course['subject_code']) ?></span>
                    <?php endif; ?>
                </span>
            </p>

            <?php if ($success): ?>
                <div class="p-6 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-2xl mb-8 border border-green-100 dark:border-green-800 flex items-center gap-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <svg class="w-8 h-8 shrink-0 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold text-lg">Submission Successful!</p>
                        <p class="text-sm opacity-90 leading-relaxed">Thank you for helping other students. Our team will review and approve your submission shortly.</p>
                    </div>
                </div>
                <a href="<?= sign_url('view_courses.php', ['branch_id' => $course['branch_id'], 'semester' => $course['semester']]) ?>" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-blue-500/30 uppercase tracking-widest">Return to Courses</a>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl mb-8 border border-red-100 dark:border-red-800 font-semibold"><?= safe($error) ?></div>
                <?php endif; ?>
                <form method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-xs font-black mb-2 ml-1 text-gray-400 dark:text-gray-500 uppercase tracking-widest">Resource Category</label>
                        <select name="material_type" class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold text-gray-700 dark:text-gray-300">
                            <option value="pyq">Previous Year Question (PYQ)</option>
                            <option value="qp_answer">Question Paper & Answer Key</option>
                            <option value="module">Course Module / Notes</option>
                            <option value="other">Other Study Material</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-2 ml-1 text-gray-400 dark:text-gray-500 uppercase tracking-widest">Title / Description</label>
                        <input type="text" name="link_name" required placeholder="e.g. Module 3 Notes / PYQ June 2023"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-2 ml-1 text-gray-400 dark:text-gray-500 uppercase tracking-widest">Resource URL</label>
                        <input type="url" name="url" required placeholder="Paste Google Drive or PDF link"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-2 ml-1 text-gray-400 dark:text-gray-500 uppercase tracking-widest">Contributor Name <span class="normal-case opacity-60">(Optional)</span></label>
                        <input type="text" name="contributor_name" placeholder="Leave blank to submit as Guest"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition shadow-xl shadow-blue-500/40 text-sm uppercase tracking-widest">
                            Submit for Review
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <p class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500 font-medium">
            Submitted resources are subject to verification by our team before they appear on the site.
        </p>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
