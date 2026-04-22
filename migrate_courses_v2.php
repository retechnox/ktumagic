<?php
include 'db.php';

try {
    // Helper to check if column exists
    function columnExists($pdo, $table, $column) {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    }

    if (!columnExists($pdo, 'courses', 'modules')) {
        echo "Adding 'modules' column...\n";
        $pdo->exec("ALTER TABLE courses ADD COLUMN modules LONGTEXT AFTER links");
        $pdo->exec("UPDATE courses SET modules = '[]'");
    } else {
        echo "'modules' column already exists.\n";
    }

    if (!columnExists($pdo, 'courses', 'qp_answers')) {
        echo "Adding 'qp_answers' column...\n";
        $pdo->exec("ALTER TABLE courses ADD COLUMN qp_answers LONGTEXT AFTER modules");
        $pdo->exec("UPDATE courses SET qp_answers = '[]'");
    } else {
        echo "'qp_answers' column already exists.\n";
    }

    // --- DATA MIGRATION ---
    echo "Starting data migration from 'links' to new columns...\n";
    $stmt = $pdo->query("SELECT id, links, modules, qp_answers FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($courses as $c) {
        $links = json_decode($c['links'] ?? '[]', true) ?: [];
        $modules = json_decode($c['modules'] ?? '[]', true) ?: [];
        $qp_answers = json_decode($c['qp_answers'] ?? '[]', true) ?: [];
        
        $newLinks = [];
        $changed = false;

        foreach ($links as $l) {
            $name = strtolower($l['link_name']);
            if (strpos($name, 'module') !== false) {
                $modules[] = $l;
                $changed = true;
            } elseif (strpos($name, 'qp') !== false || strpos($name, 'question') !== false || strpos($name, 'answer') !== false) {
                $qp_answers[] = $l;
                $changed = true;
            } else {
                $newLinks[] = $l;
            }
        }

        if ($changed) {
            $uq = $pdo->prepare("UPDATE courses SET links = ?, modules = ?, qp_answers = ? WHERE id = ?");
            $uq->execute([
                json_encode($newLinks),
                json_encode($modules),
                json_encode($qp_answers),
                $c['id']
            ]);
            echo "Migrated data for Course ID: " . $c['id'] . "\n";
        }
    }
    
    echo "Migration check complete.\n";
} catch (PDOException $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>
