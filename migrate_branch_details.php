<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized Access — Migration restricted to administrators.");
}
include 'db.php';

try {
    $sql = "ALTER TABLE branches 
            ADD COLUMN syllabus_link VARCHAR(500) DEFAULT NULL AFTER name,
            ADD COLUMN calendar_link VARCHAR(500) DEFAULT NULL AFTER syllabus_link";
    
    $pdo->exec($sql);
    echo "Successfully added syllabus_link and calendar_link columns to branches table.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Migration failed: " . $e->getMessage();
    }
}
?>
