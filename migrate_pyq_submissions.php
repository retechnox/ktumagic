<?php
include 'db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS pyq_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        link_name VARCHAR(255) NOT NULL,
        url TEXT NOT NULL,
        contributor_name VARCHAR(255),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (course_id)
    )");
    echo "Table pyq_submissions created successfully";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
