<?php
session_start();
include 'db.php';

// Check if table already exists
try {
    $pdo->query("SELECT 1 FROM sponsored_images LIMIT 1");
    echo "Table 'sponsored_images' already exists.";
} catch (PDOException $e) {
    // Table doesn't exist, create it
    $sql = "CREATE TABLE IF NOT EXISTS sponsored_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(255) NOT NULL,
        is_visible TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    try {
        $pdo->exec($sql);
        echo "Successfully created 'sponsored_images' table.";
    } catch (PDOException $ex) {
        die("Migration failed: " . $ex->getMessage());
    }
}
?>
