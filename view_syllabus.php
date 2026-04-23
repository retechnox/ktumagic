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

// Fetch course + branch + scheme
$cq = $pdo->prepare("SELECT c.*, b.name as branch_name, b.image_path as branch_image, s.name as scheme_name 
                      FROM courses c 
                      JOIN branches b ON c.branch_id = b.id 
                      JOIN schemes s ON c.scheme_id = s.id 
                      WHERE c.id = ?");
$cq->execute([$course_id]);
$course = $cq->fetch();
if (!$course) { header("Location: index.php"); exit; }

$syllabus_data = json_decode((string)$course['syllabus'], true) ?: [];

$DEFAULT_IMG = "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
$header_img = $course['branch_image'] ?: $DEFAULT_IMG;

// Convert normal Google Drive link → preview link
function toPreview($url) {
    if (!$url) return '#';
    return preg_replace('#/view(\?.*)?$#', '/preview', $url ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus — <?= safe($course['name']) ?> | KTU Magic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen text-gray-900 dark:text-white">
    <?php include 'nav.php'; ?>

    <main class="flex-grow">
        <div class="max-w-6xl mx-auto px-4 py-10">

            <!-- Header banner (Matched with view_courses.php) -->
            <div class="relative rounded-[2rem] overflow-hidden shadow-2xl mb-12 group">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
                <img referrerpolicy="no-referrer" src="<?= safe($header_img) ?>"
                     class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-700"
                     onerror="this.src='<?= $DEFAULT_IMG ?>'">

                <div class="absolute inset-0 flex flex-col items-center justify-center z-20 p-6 text-center">
                    <span class="px-4 py-1.5 bg-green-600/30 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full mb-4 tracking-widest uppercase">
                        <?= safe($course['scheme_name']) ?> • <?= safe($course['branch_name']) ?>
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black text-white font['Sora'] leading-tight drop-shadow-2xl uppercase tracking-tighter">
                        <?= safe($course['name']) ?>
                    </h1>
                </div>
            </div>

            <!-- Page Title & Stats -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-8 px-2">
                <div>
                    <h2 class="text-2xl md:text-4xl font-black text-gray-900 dark:text-white font-['Sora'] tracking-tight">
                        Course Syllabus
                    </h2>
                    <div class="flex items-center gap-3 mt-3">
                      <span class="px-4 py-1.5 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 text-xs font-bold tracking-widest uppercase rounded-full border border-green-200 dark:border-green-800">
                        <?= safe($course['subject_code']) ?>
                      </span>
                      <span class="text-gray-400 text-sm font-semibold uppercase tracking-wider">
                        Official Curriculum
                      </span>
                    </div>
                </div>

                <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-400 font-black text-sm hover:underline flex items-center gap-2">
                    ← BACK TO COURSES
                </a>
            </div>

            <?php if (empty($syllabus_data)): ?>
                <div class="flex flex-col items-center justify-center py-24 bg-white dark:bg-gray-800 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 font-['Sora']">Syllabus missing</h3>
                    <p class="text-gray-500 dark:text-gray-400">Official syllabus for this course hasn't been uploaded yet.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    <?php foreach ($syllabus_data as $s): 
                        $preview = sign_url('viewer_embed.php', ['url' => toPreview($s['url'])]);
                        preg_match('#/d/([^/]+)/#', $s['url'], $m);
                        $fileId = $m[1] ?? null;
                        $download = $fileId ? "https://drive.google.com/uc?export=download&id=".$fileId : $s['url'];
                    ?>
                        <div class="course-card group bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-800 hover:shadow-2xl hover:border-green-500 dark:hover:border-green-400 transition-all duration-300 flex flex-col overflow-hidden">
                            <div class="px-6 py-8 flex-grow text-center flex flex-col justify-center items-center">
                                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight font-['Sora'] uppercase tracking-tight line-clamp-2">
                                    <?= safe($s['link_name']) ?>
                                </h3>
                                <p class="mt-2 text-xs font-bold text-gray-400 uppercase tracking-widest">Official Syllabus</p>
                            </div>

                            <div class="px-6 pb-6 space-y-3">
                                <a href="<?= $preview ?>" target="_blank"
                                   class="w-full py-4 px-6 bg-green-600 hover:bg-green-700 text-white rounded-2xl shadow-lg shadow-green-500/20 transition-all flex items-center justify-center gap-4 font-black uppercase tracking-wider">
                                    VIEW SYLLABUS
                                </a>
                                <a href="<?= $download ?>" target="_blank"
                                   class="w-full py-3 px-6 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 hover:bg-gray-200 dark:hover:bg-gray-600 uppercase tracking-widest">
                                    Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="mt-20 pt-10 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-gray-400 text-sm font-bold uppercase tracking-[0.2em] mb-6">Need more help?</p>
                <a href="contact.php" class="inline-flex items-center gap-3 px-10 py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-[2rem] font-black text-sm hover:scale-105 transition-all shadow-2xl uppercase tracking-widest">
                    Contact Support
                </a>
            </div>

        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
