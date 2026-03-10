<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }

$course_id = intval($_GET['course_id'] ?? 0);
if (!$course_id) { header("Location: index.php"); exit; }

$cq = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cq->execute([$course_id]);
$course = $cq->fetch();
if (!$course) { header("Location: index.php"); exit; }

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link_name = trim($_POST['link_name'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $contributor = trim($_POST['contributor_name'] ?? '');

    if (empty($link_name) || empty($url)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pyq_submissions (course_id, link_name, url, contributor_name) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$course_id, $link_name, $url, $contributor])) {
            $success = true;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit PYQ — <?= safe($course['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 font-['Inter']">
    <?php include 'nav.php'; ?>
    <div class="max-w-xl mx-auto px-4 py-32">
        <a href="view_courses.php?branch_id=<?= $course['branch_id'] ?>&semester=<?= $course['semester'] ?>" class="text-blue-600 dark:text-blue-400 hover:underline mb-8 inline-flex items-center gap-2 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Courses
        </a>
        
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full -mr-16 -mt-16"></div>
            
            <h1 class="text-3xl font-black mb-2 font-['Sora'] tracking-tight">Submit PYQ</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-10 font-medium">Contribute to the community: <span class="text-blue-600 dark:text-blue-400"><?= safe($course['name']) ?></span></p>

            <?php if ($success): ?>
                <div class="p-6 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-2xl mb-8 border border-green-100 dark:border-green-800 flex items-center gap-4">
                    <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Successfully Submitted!</p>
                        <p class="text-sm opacity-90">Thank you for your contribution. An admin will review and approve it shortly.</p>
                    </div>
                </div>
                <a href="view_courses.php?branch_id=<?= $course['branch_id'] ?>&semester=<?= $course['semester'] ?>" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition shadow-lg shadow-blue-500/30">Return to Courses</a>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl mb-8 border border-red-100 dark:border-red-800"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold mb-2 ml-1 text-gray-700 dark:text-gray-300 uppercase tracking-wider">Link Title</label>
                        <input type="text" name="link_name" required placeholder="e.g. QP Dec 2023 / Answer Key"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 ml-1 text-gray-700 dark:text-gray-300 uppercase tracking-wider">Resource URL</label>
                        <input type="url" name="url" required placeholder="Paste Google Drive / PDF link here"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 ml-1 text-gray-700 dark:text-gray-300 uppercase tracking-wider">Your Name <span class="text-gray-400 font-normal lowercase">(optional)</span></label>
                        <input type="text" name="contributor_name" placeholder="Leave blank to remain anonymous"
                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition shadow-xl shadow-blue-500/40 text-lg uppercase tracking-widest">
                            Submit for Approval
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            By submitting, you agree to share this academic resource with the KTU Magic community.
        </p>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
