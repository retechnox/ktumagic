<?php
include 'db.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT destination_url FROM short_links WHERE slug = ?");
$stmt->execute([$slug]);
$link = $stmt->fetch();

if (!$link) {
    header("Location: 404.php");
    exit();
}

$dest = $link['destination_url'];

// Check if it's an internal link that needs a signature
// We assume internal links start with a filename (e.g. view_link.php) or /
$is_internal = false;
$parsed = parse_url($dest);

// If host is empty or matches current domain, it's internal
// For simplicity, we check if it starts with 'view_' or matches known internal patterns
if (!isset($parsed['host']) || $parsed['host'] === 'ktumagic.in' || $parsed['host'] === 'www.ktumagic.in') {
    $is_internal = true;
}

if ($is_internal) {
    $path = '/' . ltrim($parsed['path'] ?? '', '/');
    $query = $parsed['query'] ?? '';
    parse_str($query, $params);
    $redirect_url = sign_url($path, $params);
} else {
    // Security Hardening: External URL validation
    $scheme = strtolower($parsed['scheme'] ?? '');
    if (!in_array($scheme, ['http', 'https'])) {
        header("Location: /404.php");
        exit();
    }
    $redirect_url = $dest;
}

// Security Hardening: Prevent HTTP Response Splitting (CRLF Injection)
$redirect_url = str_replace(["\r", "\n"], '', $redirect_url);

header("Location: " . $redirect_url, true, 302);
exit();
