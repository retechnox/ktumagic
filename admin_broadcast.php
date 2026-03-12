<?php
include 'db.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit;
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $link = trim($_POST['link'] ?? '');
    
    if (!$title || !$body) {
        $err = "Title and body are required.";
    } else {
        $appEnv = getenv('APP_ENV') ?: 'development';
        $wsSecret = getenv('WS_ADMIN_SECRET') ?: 'magic_ktu_admin_secret_2026';
        // In production Nginx routes /ws-api → Node on 8080; in dev hit it directly
        $wsApiUrl = ($appEnv === 'production')
            ? 'http://127.0.0.1:8080/broadcast'
            : 'http://127.0.0.1:8080/broadcast';

        $data = json_encode([
            'secret' => $wsSecret,
            'title'  => $title,
            'body'   => $body,
            'link'   => $link
        ]);
        
        $ch = curl_init($wsApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpcode == 200) {
            $resData = json_decode($response, true);
            $wsCount = $resData['deliveredWS'] ?? 0;
            $pushCount = $resData['deliveredPush'] ?? 0;
            $msg = "Broadcast sent successfully! Delivered to $wsCount live users and $pushCount background devices.";
        } else {
            $err = "Failed to send broadcast. Ensure WebSocket server is running. Response: $response";
            }
        }
    } catch (Exception $e) {
        $err = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Broadcast Notifications</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
</style>
</head>
<body class="flex min-h-screen">

<!-- Sidebar Sidebar Sidebar Sideba -->
<aside class="w-64 bg-white shadow-xl flex flex-col hidden md:flex">
    <div class="h-16 flex items-center justify-center border-b border-gray-100">
        <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Admin Panel</h1>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="admin.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="admin_notes.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Manage Notes
        </a>
        <a href="admin_submissions.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Submissions
        </a>
        <a href="admin_broadcast.php" class="flex items-center px-4 py-3 text-blue-700 bg-blue-50 font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Live Broadcasts
        </a>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <a href="logout.php" class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </a>
    </div>
</aside>

<main class="flex-1 overflow-y-auto w-full">
    <div class="p-8 max-w-4xl mx-auto">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Broadcast Notifications</h2>
                <p class="text-gray-500 mt-1">Send real-time desktop push notifications to all connected users.</p>
            </div>
            <a href="admin.php" class="md:hidden text-blue-600 font-medium">&larr; Dashboard</a>
        </div>

        <?php if($msg): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 rounded-r-xl shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="ml-3"><p class="text-sm font-medium text-green-800"><?= htmlspecialchars($msg) ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($err): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="ml-3"><p class="text-sm font-medium text-red-800"><?= htmlspecialchars($err) ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Compose Message</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                    Server Online
                </span>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notification Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required maxlength="50" placeholder="e.g. Important Announcement!" class="w-full h-11 px-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message Body <span class="text-red-500">*</span></label>
                        <textarea name="body" required rows="3" maxlength="150" placeholder="e.g. A new module has been added to Programming in C." class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"></textarea>
                        <p class="mt-1.5 text-xs text-gray-500 float-right">Keep it brief (max 150 chars).</p>
                    </div>
                    
                    <div class="clear-both"></div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Action Link URL (Optional)</label>
                        <input type="url" name="link" placeholder="e.g. https://ktumagic.com/pyq.php" class="w-full h-11 px-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                        <p class="mt-1.5 text-xs text-gray-500">When users click the notification, they will be redirected to this link.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Broadcast Now
                        </button>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-sm text-gray-500">
                <strong>Note:</strong> Broadcasts are delivered instantly to all users currently viewing the website who have granted notification permissions.
            </div>
        </div>

    </div>
</main>
<script>
  /* -------------------------
      PREVENT DOUBLE SUBMISSION
  -------------------------- */
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
      if (this.dataset.submitting) {
        e.preventDefault();
        return;
      }
      this.dataset.submitting = 'true';
      const btn = this.querySelector('button[type="submit"]');
      if (btn) {
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';
        btn.classList.add('opacity-50', 'cursor-not-allowed');
      }
    });
  });
</script>
</body>
</html>
