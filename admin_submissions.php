<?php
include 'db.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        check_csrf();
        $sub_id = intval($_POST['sub_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Fetch submission
        $sq = $pdo->prepare("SELECT * FROM pyq_submissions WHERE id = ?");
        $sq->execute([$sub_id]);
        $sub = $sq->fetch();

        if ($sub) {
            // Determine material type
            $type = $sub['material_type'] ?? 'pyq';
            
            if ($type === 'pyq') {
                $col = 'pyqs';
            } elseif ($type === 'module') {
                $col = 'modules';
            } elseif ($type === 'qp_answer') {
                $col = 'qp_answers';
            } else {
                $col = 'links';
            }

            // Determine target column data
            $cq = $pdo->prepare("SELECT $col FROM courses WHERE id = ?");
            $cq->execute([$sub['course_id']]);
            $course = $cq->fetch();
            $data = json_decode($course[$col] ?? '[]', true) ?: [];
            
            $data[] = [
                'link_name' => $sub['link_name'],
                'url' => $sub['url']
            ];
            
            $uq = $pdo->prepare("UPDATE courses SET $col = ? WHERE id = ?");
            $uq->execute([json_encode($data), $sub['course_id']]);

            // Mark submission as approved
            $pdo->prepare("UPDATE pyq_submissions SET status = 'approved' WHERE id = ?")->execute([$sub_id]);
        }
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE pyq_submissions SET status = 'rejected' WHERE id = ?")->execute([$sub_id]);
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM pyq_submissions WHERE id = ?")->execute([$sub_id]);
    }
        header("Location: admin_submissions.php");
        exit();
    } catch (Exception $e) {
        die("Security Error: " . $e->getMessage());
    }
}

$submissions = $pdo->query("
    SELECT s.*, c.name as course_name 
    FROM pyq_submissions s 
    JOIN courses c ON s.course_id = c.id 
    ORDER BY s.created_at DESC
")->fetchAll();

function getMaterialBadge($type) {
    if ($type === 'pyq') return '<span class="badge bg-secondary">PYQ</span>';
    if ($type === 'qp_answer') return '<span class="badge bg-info text-dark">QP & Answer</span>';
    if ($type === 'module') return '<span class="badge bg-primary">Module</span>';
    return '<span class="badge bg-dark">Other</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Resource Submissions — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table thead th { border-top: none; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; color: #6c757d; }
        .badge { border-radius: 8px; padding: 6px 12px; font-weight: 600; }
        .btn { border-radius: 12px; font-weight: 600; transition: all 0.2s; }
        .h1, h1 { font-family: 'Sora', sans-serif; font-weight: 800; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="mb-0">Resource Submissions</h1>
                <p class="text-muted">Review and approve user-contributed study materials.</p>
            </div>
            <a href="admin.php" class="btn btn-outline-dark px-4">Back to Dashboard</a>
        </div>

        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th class="ps-4 py-3">Course / Subject</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Link Name</th>
                            <th class="py-3">Resource</th>
                            <th class="py-3">Contributor</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No submissions found.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($s['course_name']) ?></div>
                                    <small class="text-muted">ID: #<?= $s['course_id'] ?></small>
                                </td>
                                <td><?= getMaterialBadge($s['material_type'] ?? 'pyq') ?></td>
                                <td><?= htmlspecialchars($s['link_name']) ?></td>
                                <td><a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" class="text-primary text-decoration-none fw-semibold">View URL</a></td>
                                <td><?= htmlspecialchars($s['contributor_name'] ?: 'Anonymous') ?></td>
                                <td><small class="text-muted"><?= date('M j, Y', strtotime($s['created_at'])) ?></small></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $s['status'] === 'pending' ? 'warning text-dark' : ($s['status'] === 'approved' ? 'success' : 'danger') ?>">
                                        <?= strtoupper($s['status']) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <?php if ($s['status'] === 'pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="sub_id" value="<?= $s['id'] ?>">
                                                <button name="action" value="approve" class="btn btn-sm btn-success px-3">Approve</button>
                                                <button name="action" value="reject" class="btn btn-sm btn-danger px-3">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this submission?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="sub_id" value="<?= $s['id'] ?>">
                                            <button name="action" value="delete" class="btn btn-sm btn-light ms-1" title="Delete record">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      /* -------------------------
          PREVENT DOUBLE SUBMISSION
      -------------------------- */
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          if (this.dataset.submitting) {
            e.preventDefault();
            return;
          }
          this.dataset.submitting = 'true';
          const btn = e.submitter || this.querySelector('button[type="submit"], .btn-success, .btn-danger');
          if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btn.classList.add('disabled');
            btn.style.pointerEvents = 'none';
          }
        });
      });
    </script>
</body>
</html>
