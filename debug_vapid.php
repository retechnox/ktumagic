<?php
include_once __DIR__ . '/db.php';
echo "VAPID_PUBLIC_KEY=" . getenv('VAPID_PUBLIC_KEY') . "\n";
echo "VAPID_PRIVATE_KEY=" . getenv('VAPID_PRIVATE_KEY') . "\n";
?>
