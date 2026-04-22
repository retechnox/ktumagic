<?php
include 'db.php';

try {
    // Add material_type column to pyq_submissions
    $pdo->exec("ALTER TABLE pyq_submissions ADD COLUMN material_type ENUM('pyq', 'qp_answer', 'module', 'other') DEFAULT 'pyq' AFTER url");
    echo "Successfully added material_type column to pyq_submissions table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column material_type already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
