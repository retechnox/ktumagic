<?php
// viewer_embed.php
// Usage: viewer_embed.php?url=<FULL_GOOGLE_DRIVE_VIEW_URL>

$raw = $_GET['url'] ?? '';

if (!$raw) {
    die("<h3>No file provided.</h3>");
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
