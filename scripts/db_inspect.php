<?php
include __DIR__ . '/../db.php';

try {
    echo "--- COURSES TABLE ---\n";
    $s = $pdo->query("SHOW CREATE TABLE courses")->fetch();
    echo $s["Create Table"] . "\n\n";

    echo "--- TABLES REFERENCING courses ---\n";
    $q = $pdo->prepare("
        SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME = 'courses' AND REFERENCED_TABLE_SCHEMA = ?
    ");
    $q->execute([getenv('DB_DATABASE') ?: 'defaultdb']);
    $res = $q->fetchAll();
    if ($res) {
        foreach ($res as $r) {
            echo "Table: {$r['TABLE_NAME']}, Column: {$r['COLUMN_NAME']}, Constraint: {$r['CONSTRAINT_NAME']}\n";
        }
    } else {
        echo "No foreign keys found referencing courses.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
