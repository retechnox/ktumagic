<?php
// viewer_embed.php
// Usage: viewer_embed.php?url=<FULL_GOOGLE_DRIVE_VIEW_URL>

include 'db.php';
$raw = $_GET['url'] ?? '';

if (!$raw) {
    die("<h3>No file provided.</h3>");
}

// Security Hardening: Enforce domain whitelist for viewer
$parsed = parse_url($raw);
$host = strtolower($parsed['host'] ?? '');
$allowed = false;
if (str_contains($host, 'drive.google.com')) $allowed = true;
if (str_contains($host, 'mbcetlibrary.wordpress.com')) $allowed = true;

if (!$allowed) {
    die("<h3>Security Error: This domain is not authorized for embedding.</h3>");
}

// Optimization: Convert /view to /preview for proper iframing (prevents SAMEORIGIN blocks)
if (str_contains($raw, '/file/d/')) {
    // Extract base link before any trailing slashes or params
    // Format: https://drive.google.com/file/d/ID/view... -> https://drive.google.com/file/d/ID/preview
    $raw = preg_replace('/\/view(\?.*)?$/', '/preview', $raw);
    if (!str_ends_with($raw, '/preview')) {
        $raw = rtrim($raw, '/') . '/preview';
    }
}

$url = htmlspecialchars($raw);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Viewer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { margin: 0; background:#000; }
iframe {
    width: 100vw;
    height: 100vh;
    border: none;
}
</style>
</head>
<body>

<iframe src="<?= $url ?>" allow="autoplay"></iframe>

</body>
</html>
