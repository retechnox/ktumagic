<?php
// Secure Google Drive Proxy Viewer

include 'db.php';

$file_id = intval($_GET['file'] ?? 0);
if (!$file_id) die("Invalid file ID");

// Fetch drive file id from DB
$stmt = $pdo->prepare("SELECT drive_id, link_name FROM file_links WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) die("File not found.");

$drive_id = $file['drive_id'];

// Direct preview URL (Google does NOT show download button)
$google_preview_url = "https://drive.google.com/uc?export=preview&id=" . $drive_id;

// Fetch file content
$ch = curl_init($google_preview_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

// Force inline view - block download
header("Content-Type: $content_type");
header("Content-Disposition: inline; filename=\"secure_content\"");
header("X-Frame-Options: SAMEORIGIN");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

// Output file
echo $data;
?>
