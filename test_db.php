<?php
include 'db.php';
$stmt = $pdo->query("DESCRIBE branches");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $pdo->query("SELECT id, name, redirect_branch_id FROM branches WHERE id=29");
print_r($stmt->fetch());
?>
