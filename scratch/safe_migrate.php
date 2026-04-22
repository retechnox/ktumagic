<?php
include 'db.php';

$dry_run = true; // Set to false to actually perform the migration

try {
    $stmt = $pdo->query("SELECT id, name, links, pyqs, syllabus FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats = [
        'total_courses' => count($courses),
        'pyq_moved' => 0,
        'syllabus_moved' => 0,
        'courses_affected' => 0
    ];

    $pyq_keywords = ['PYQ', 'Previous Year', 'Question Paper', 'QP', 'Series Test', 'Model Exam', 'Question Bank'];
    $syllabus_keywords = ['Syllabus'];

    echo "Starting Migration (Dry Run: " . ($dry_run ? "YES" : "NO") . ")\n";
    echo "========================================\n";

    foreach ($courses as $c) {
        $links = json_decode($c['links'] ?? '[]', true) ?: [];
        $pyqs = json_decode($c['pyqs'] ?? '[]', true) ?: [];
        $syllabus = json_decode($c['syllabus'] ?? '[]', true) ?: [];

        $new_links = [];
        $moved = false;

        foreach ($links as $l) {
            $name = $l['link_name'] ?? '';
            $is_pyq = false;
            $is_syllabus = false;

            // Check for PYQ keywords
            foreach ($pyq_keywords as $k) {
                if (stripos($name, $k) !== false) {
                    $is_pyq = true;
                    break;
                }
            }

            // Check for Syllabus keywords
            foreach ($syllabus_keywords as $k) {
                if (stripos($name, $k) !== false) {
                    $is_syllabus = true;
                    break;
                }
            }
            
            // Explicitly exclude "Module Notes" even if they mention QP (rare but safe)
            if (stripos($name, 'Module') !== false || stripos($name, 'Note') !== false) {
                $is_pyq = false;
                $is_syllabus = false;
            }

            if ($is_pyq) {
                $pyqs[] = $l;
                $stats['pyq_moved']++;
                $moved = true;
                echo "[PYQ] '{$name}' moved in course '{$c['name']}'\n";
            } elseif ($is_syllabus) {
                $syllabus[] = $l;
                $stats['syllabus_moved']++;
                $moved = true;
                echo "[SYLLABUS] '{$name}' moved in course '{$c['name']}'\n";
            } else {
                $new_links[] = $l;
            }
        }

        if ($moved) {
            $stats['courses_affected']++;
            if (!$dry_run) {
                $upd = $pdo->prepare("UPDATE courses SET links = ?, pyqs = ?, syllabus = ? WHERE id = ?");
                $upd->execute([
                    json_encode($new_links),
                    json_encode($pyqs),
                    json_encode($syllabus),
                    $c['id']
                ]);
            }
        }
    }

    echo "========================================\n";
    echo "Migration Summary:\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
