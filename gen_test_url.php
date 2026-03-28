<?php
include 'db.php';
$params = ['branch_id' => 29];
echo sign_url('view_semesters.php', $params) . "\n";
?>
