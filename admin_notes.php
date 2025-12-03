<?php

include __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    header("Location: login.php");
    exit();
}

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 MB
$allowedImageExt = ['png','jpg','jpeg','gif','webp'];
$allowedMime = ['image/png','image/jpeg','image/gif','image/webp'];

// Start session for flash and CSRF
if (session_status() === PHP_SESSION_NONE) session_start();

// Simple flash helper
function flash($msg = null, $type = 'info') {
    if ($msg === null) {
        $f = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $f;
    }
    $_SESSION['flash'][] = ['msg'=>$msg,'type'=>$type];
}

// CSRF helpers
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
function csrf_field() {
    $t = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES);
    return "<input type='hidden' name='csrf_token' value='{$t}'>";
}
function check_csrf() {
    $p = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], (string)$p)) {
        throw new Exception('Invalid CSRF token.');
    }
}

// -----------------------------
// Utilities
// -----------------------------
function safe($v) { return htmlspecialchars((string)$v, ENT_QUOTES); }

function makeUploadDir() {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
}

// Secure image upload: returns relative path (web path), or null
function uploadImageFile($fieldName, $allowedExt, $allowedMime, $maxSize) {
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) return null;

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File is too large. Max ' . ($maxSize / (1024*1024)) . ' MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMime)) {
        throw new Exception('Unsupported file type.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        throw new Exception('Unsupported file extension.');
    }

    makeUploadDir();
    $newName = uniqid('img_', true) . '.' . $ext;
    $dest = UPLOAD_DIR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new Exception('Failed to move uploaded file.');
    }

    // return web path relative to project root (assumes uploads folder is accessible at /uploads/)
    // adjust if your uploads path is different
    $rel = '/uploads/' . $newName;
    return $rel;
}

// Delete image by relative path
function deleteImageIfExists($relPath) {
    if (!$relPath) return;
    // convert to absolute
    $abs = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . ltrim($relPath, '/\\');
    if ($abs && file_exists($abs)) @unlink($abs);
}

// -----------------------------
// Actions (POST)
// -----------------------------
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF for all POST actions
        check_csrf();

        $action = $_POST['action'] ?? '';

        if ($action === 'add_scheme') {
            $name = trim($_POST['scheme_name'] ?? '');
            if ($name === '') throw new Exception('Scheme name is required.');
            $stmt = $pdo->prepare('INSERT INTO schemes (name) VALUES (?)');
            $stmt->execute([$name]);
            flash('Scheme added successfully.', 'success');
        }

        elseif ($action === 'edit_scheme') {
            $id = intval($_POST['scheme_id'] ?? 0);
            $name = trim($_POST['scheme_name'] ?? '');
            if (!$id) throw new Exception('Invalid scheme id.');
            if ($name === '') throw new Exception('Scheme name is required.');
            $stmt = $pdo->prepare('UPDATE schemes SET name = ? WHERE id = ?');
            $stmt->execute([$name, $id]);
            flash('Scheme updated.', 'success');
        }

        elseif ($action === 'delete_scheme') {
            $id = intval($_POST['scheme_id'] ?? 0);
            if (!$id) throw new Exception('Invalid scheme id.');
            // optionally check dependent branches/courses
            $stmt = $pdo->prepare('DELETE FROM schemes WHERE id = ?');
            $stmt->execute([$id]);
            flash('Scheme deleted.', 'success');
        }

        elseif ($action === 'add_branch') {
            $scheme_id = intval($_POST['scheme_id'] ?? 0);
            $name = trim($_POST['branch_name'] ?? '');
            if (!$scheme_id) throw new Exception('Select a scheme.');
            if ($name === '') throw new Exception('Branch name is required.');

            $image = null;
            if (!empty($_FILES['branch_image']) && $_FILES['branch_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $image = uploadImageFile('branch_image', $allowedImageExt, $allowedMime, MAX_UPLOAD_SIZE);
            }

            $stmt = $pdo->prepare('INSERT INTO branches (scheme_id, name, image_path) VALUES (?, ?, ?)');
            $stmt->execute([$scheme_id, $name, $image]);
            flash('Branch added.', 'success');
        }

        elseif ($action === 'edit_branch') {
            $branch_id = intval($_POST['branch_id'] ?? 0);
            $branch_name = trim($_POST['branch_name'] ?? '');
            if (!$branch_id) throw new Exception('Invalid branch id.');
            if ($branch_name === '') throw new Exception('Branch name required.');

            $image = null;
            if (!empty($_FILES['branch_image_edit']) && $_FILES['branch_image_edit']['error'] !== UPLOAD_ERR_NO_FILE) {
                // delete old first
                $old = $pdo->prepare('SELECT image_path FROM branches WHERE id = ?');
                $old->execute([$branch_id]);
                $oldp = $old->fetchColumn();
                $image = uploadImageFile('branch_image_edit', $allowedImageExt, $allowedMime, MAX_UPLOAD_SIZE);
                if ($oldp) deleteImageIfExists($oldp);
            }

            if ($image) {
                $stmt = $pdo->prepare('UPDATE branches SET name = ?, image_path = ? WHERE id = ?');
                $stmt->execute([$branch_name, $image, $branch_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE branches SET name = ? WHERE id = ?');
                $stmt->execute([$branch_name, $branch_id]);
            }
            flash('Branch updated.', 'success');
        }

        elseif ($action === 'delete_branch') {
            $branch_id = intval($_POST['branch_id'] ?? 0);
            if (!$branch_id) throw new Exception('Invalid branch id.');
            // delete branch images and optionally cascade delete courses
            $old = $pdo->prepare('SELECT image_path FROM branches WHERE id = ?');
            $old->execute([$branch_id]);
            $oldp = $old->fetchColumn();
            $del = $pdo->prepare('DELETE FROM branches WHERE id = ?');
            $del->execute([$branch_id]);
            if ($oldp) deleteImageIfExists($oldp);
            flash('Branch deleted.', 'success');
        }

        elseif ($action === 'add_course') {
            $scheme_id = intval($_POST['scheme_id'] ?? 0);
            $branch_id = intval($_POST['branch_id'] ?? 0);
            $course_name = trim($_POST['course_name'] ?? '');
            $semester = intval($_POST['semester'] ?? 0) ?: null;
            if (!$scheme_id || !$branch_id) throw new Exception('Select scheme and branch.');
            if ($course_name === '') throw new Exception('Course name required.');

            $links = $_POST['links'] ?? [];
            // sanitize link array: only keep entries where url and name exist
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url];
            }

            $image = null;
            if (!empty($_FILES['course_image']) && $_FILES['course_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $image = uploadImageFile('course_image', $allowedImageExt, $allowedMime, MAX_UPLOAD_SIZE);
            }

            $stmt = $pdo->prepare('INSERT INTO courses (branch_id, scheme_id, name, links, image_path, semester) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$branch_id, $scheme_id, $course_name, json_encode($validLinks), $image, $semester]);
            flash('Course added.', 'success');
        }

        elseif ($action === 'edit_course') {
            $course_id = intval($_POST['course_id'] ?? 0);
            $course_name = trim($_POST['course_name_edit'] ?? '');
            $semester = intval($_POST['semester_edit'] ?? 0) ?: null;
            if (!$course_id) throw new Exception('Invalid course id.');
            if ($course_name === '') throw new Exception('Course name required.');

            $image = null;
            if (!empty($_FILES['course_image_edit']) && $_FILES['course_image_edit']['error'] !== UPLOAD_ERR_NO_FILE) {
                // delete old
                $old = $pdo->prepare('SELECT image_path FROM courses WHERE id = ?');
                $old->execute([$course_id]);
                $oldp = $old->fetchColumn();
                $image = uploadImageFile('course_image_edit', $allowedImageExt, $allowedMime, MAX_UPLOAD_SIZE);
                if ($oldp) deleteImageIfExists($oldp);
            }

            if ($image) {
                $stmt = $pdo->prepare('UPDATE courses SET name = ?, semester = ?, image_path = ? WHERE id = ?');
                $stmt->execute([$course_name, $semester, $image, $course_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE courses SET name = ?, semester = ? WHERE id = ?');
                $stmt->execute([$course_name, $semester, $course_id]);
            }
            flash('Course updated.', 'success');
        }

        elseif ($action === 'delete_course') {
            $course_id = intval($_POST['course_id'] ?? 0);
            if (!$course_id) throw new Exception('Invalid course id.');
            $old = $pdo->prepare('SELECT image_path FROM courses WHERE id = ?');
            $old->execute([$course_id]);
            $oldp = $old->fetchColumn();
            $del = $pdo->prepare('DELETE FROM courses WHERE id = ?');
            $del->execute([$course_id]);
            if ($oldp) deleteImageIfExists($oldp);
            flash('Course deleted.', 'success');
        }

        elseif ($action === 'save_links') {
            $course_id = intval($_POST['course_id'] ?? 0);
            if (!$course_id) throw new Exception('Invalid course id.');
            $links = $_POST['links'] ?? [];
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url];
            }
            $stmt = $pdo->prepare('UPDATE courses SET links = ? WHERE id = ?');
            $stmt->execute([json_encode($validLinks), $course_id]);
            flash('Links saved.', 'success');
        }

        // After any POST action, redirect to avoid form resubmission
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
} catch (Exception $e) {
    // Catch exceptions thrown during actions
    flash('Error: ' . $e->getMessage(), 'danger');
    // Do NOT exit: allow page to render with flash messages
}

// -----------------------------
// Fetch data for rendering (safe reads)
// -----------------------------
$schemes = $pdo->query('SELECT * FROM schemes ORDER BY name')->fetchAll();
$branches = $pdo->query('SELECT * FROM branches ORDER BY name')->fetchAll();
$courses = $pdo->query("
    SELECT c.*, s.name AS scheme_name, b.name AS branch_name
    FROM courses c
    LEFT JOIN schemes s ON s.id = c.scheme_id
    LEFT JOIN branches b ON b.id = c.branch_id
    ORDER BY c.id DESC
")->fetchAll();

// For selected course links
$selected_course = null;
$links = [];
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $q = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $q->execute([$course_id]);
    $selected_course = $q->fetch();
    if ($selected_course) {
        $links = json_decode($selected_course['links'] ?: '[]', true) ?: [];
    }
}

// -----------------------------
// Render
// -----------------------------
$flashes = flash(); // get flashes

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin — Schemes / Branches / Courses</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --sidebar-width: 260px;
      --sidebar-collapsed: 70px;
    }
    body{background:#f4f6fb}
    .sidebar{
      width:var(--sidebar-width);
      min-height:100vh;
      background:linear-gradient(180deg,#ffffff, #f8fbff);
      border-right:1px solid #e6eef8;
      transition:width .18s ease;
      padding:1rem;
    }
    .sidebar .brand{font-weight:700; font-size:1.15rem; letter-spacing:0.2px}
    .nav-link.active{background:#e9f2ff;border-radius:8px;color:#0d6efd}
    .card-rounded{border-radius:12px}
    .table-image{width:64px;height:44px;object-fit:cover;border-radius:6px}
    .branch-img{height:120px;object-fit:cover;width:100%;border-radius:10px 10px 0 0}
    .semester-pill{cursor:pointer;padding:8px 10px;border-radius:999px;border:1px solid #e7eefc;margin:4px}
    .semester-pill.active{background:#0d6efd;color:#fff;border-color:#0d6efd}
    .link-row .form-control{border-radius:6px}
    .flash{margin-bottom:16px}

    body.sidebar-collapsed .sidebar{
      width:var(--sidebar-collapsed) !important;
    }
    body.sidebar-collapsed .sidebar .brand,
    body.sidebar-collapsed .sidebar .text-muted,
    body.sidebar-collapsed .sidebar .nav-caption{
      display:none !important;
    }
    body.sidebar-collapsed .sidebar .nav-link{
      text-align:center;
      padding-left:0.5rem;
      padding-right:0.5rem;
    }
    /* small screens: sidebar hidden by default and slides in */
    @media (max-width: 991px){
      .sidebar{
        position:fixed;
        left:-120%;
        top:0;
        z-index:1050;
        height:100vh;
        overflow:auto;
        transition:left .22s ease;
      }
      .sidebar.show{left:0}
    }
  </style>
</head>
<body>
<div class="d-flex">
  <!-- Sidebar -->
  <aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="mb-4 d-flex align-items-center justify-content-between">
      <div>
        <div class="brand mb-1">Admin Panel</div>
        <div class="text-muted small">Schemes • Branches • Courses</div>
      </div>
      <div class="d-flex align-items-center">
        <button class="btn btn-sm btn-outline-secondary me-2 d-none d-lg-inline" id="collapseSidebarBtn" title="Toggle sidebar">☰</button>
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="mobileCloseSidebarBtn" title="Close menu">✕</button>
      </div>
    </div>

    <nav class="nav flex-column mb-3">
      <a href="#" class="nav-link active" data-target="dashboardSection"><span class="nav-caption">Dashboard</span></a>
      <a href="#" class="nav-link" data-target="schemesSection"><span class="nav-caption">Schemes</span></a>
      <a href="#" class="nav-link" data-target="branchesSection"><span class="nav-caption">Branches</span></a>
      <a href="#" class="nav-link" data-target="coursesSection"><span class="nav-caption">Courses</span></a>
      <a href="#" class="nav-link" data-target="linksSection"><span class="nav-caption">Course Links</span></a>
    </nav>

    <div class="mt-auto">
      <small class="text-muted">Logged in as Admin</small>
    </div>
  </aside>

  <!-- Content -->
  <div class="flex-fill" style="min-height:100vh">
    <!-- topbar -->
    <header class="d-flex align-items-center justify-content-between bg-white p-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none" id="mobileMenuBtn">☰</button>
        <h4 class="mb-0">Manage Courses</h4>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary btn-sm" id="btnTableMode">Table</button>
        <button class="btn btn-outline-secondary btn-sm" id="btnCardMode">Cards</button>
      </div>
    </header>

    <main class="p-4">
      <?php if (!empty($flashes)): ?>
        <div class="flash">
          <?php foreach ($flashes as $f): ?>
            <div class="alert alert-<?= ($f['type'] ?? 'info') ?> mb-2"><?= safe($f['msg']) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Sections -->
      <!-- Dashboard (default visible) -->
      <section id="dashboardSection" class="page-section">
        <div id="tableMode" class="card card-rounded p-3 mb-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Courses</h5>
            <div class="d-flex gap-2 align-items-center">
              <div class="form-inline d-flex gap-2">
                <select id="tm_filter_scheme" class="form-select form-select-sm">
                  <option value="">All schemes</option>
                  <?php foreach ($schemes as $s): ?>
                    <option value="<?= safe(strtolower($s['name'])) ?>"><?= safe($s['name']) ?></option>
                  <?php endforeach; ?>
                </select>

                <select id="tm_filter_branch" class="form-select form-select-sm">
                  <option value="">All branches</option>
                  <?php foreach ($branches as $b): ?>
                    <option value="<?= safe(strtolower($b['name'])) ?>"><?= safe($b['name']) ?></option>
                  <?php endforeach; ?>
                </select>

                <select id="tm_filter_semester" class="form-select form-select-sm">
                  <option value="">All sem</option>
                  <?php for ($i=1;$i<=8;$i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                  <?php endfor; ?>
                </select>

                <input id="tm_search" class="form-control form-control-sm" placeholder="Search course..." style="width:220px;">
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table id="tm_table" class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th><th>Scheme</th><th>Branch</th><th>Semester</th><th>Course</th><th>Image</th><th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($courses as $c): ?>
                  <tr data-scheme="<?= safe(strtolower($c['scheme_name'])) ?>"
                      data-branch="<?= safe(strtolower($c['branch_name'])) ?>"
                      data-semester="<?= safe($c['semester']) ?>"
                      data-name="<?= safe(strtolower($c['name'])) ?>">
                    <td><?= $c['id'] ?></td>
                    <td><?= safe($c['scheme_name']) ?></td>
                    <td><?= safe($c['branch_name']) ?></td>
                    <td><?= safe($c['semester']) ?></td>
                    <td><?= safe($c['name']) ?></td>
                    <td><?= !empty($c['image_path']) ? "<img src='" . safe($c['image_path']) . "' class='table-image' />" : '—' ?></td>
                    <td class="text-end">
                      <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit-course" data-id="<?= $c['id'] ?>" data-name="<?= safe($c['name']) ?>" data-sem="<?= safe($c['semester']) ?>">Edit</button>

                      <form method="POST" style="display:inline-block" onsubmit="return confirm('Delete course?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_course">
                        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (count($courses) === 0): ?>
                  <tr><td colspan="7" class="text-center text-muted">No courses yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Schemes -->
      <section id="schemesSection" class="page-section" style="display:none">
        <div class="card card-rounded p-3 mb-3">
          <h5 class="mb-3">Add / Manage Schemes</h5>
          <div class="row">
            <div class="col-md-4">
              <form method="POST" action="">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add_scheme">
                <div class="mb-2">
                  <label class="form-label small">Scheme name</label>
                  <input type="text" name="scheme_name" class="form-control form-control-sm" required>
                </div>
                <div class="d-grid"><button class="btn btn-primary btn-sm">Add Scheme</button></div>
              </form>
            </div>

            <div class="col-md-8">
              <table class="table table-sm">
                <thead><tr><th>ID</th><th>Name</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                  <?php foreach ($schemes as $s): ?>
                    <tr>
                      <td><?= $s['id'] ?></td>
                      <td><?= safe($s['name']) ?></td>
                      <td class="text-end">
                        <!-- edit (simple inline form) -->
                        <form method="POST" class="d-inline-block" style="max-width:240px">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="edit_scheme">
                          <input type="hidden" name="scheme_id" value="<?= $s['id'] ?>">
                          <div class="input-group input-group-sm">
                            <input type="text" name="scheme_name" class="form-control" value="<?= safe($s['name']) ?>" required>
                            <button class="btn btn-sm btn-outline-primary">Save</button>
                          </div>
                        </form>

                        <form method="POST" class="d-inline-block" onsubmit="return confirm('Delete scheme?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="delete_scheme">
                          <input type="hidden" name="scheme_id" value="<?= $s['id'] ?>">
                          <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- Branches -->
      <section id="branchesSection" class="page-section" style="display:none">
        <div class="row g-3">
          <div class="col-lg-4">
            <div class="card card-rounded p-3">
              <h6 class="mb-3">Add Branch</h6>
              <form method="POST" action="" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add_branch">
                <div class="mb-2">
                  <label class="form-label small">Select scheme</label>
                  <select name="scheme_id" class="form-select form-select-sm" required>
                    <option value="">Select</option>
                    <?php foreach ($schemes as $s): ?>
                      <option value="<?= $s['id'] ?>"><?= safe($s['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label small">Branch name</label>
                  <input type="text" name="branch_name" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                  <label class="form-label small">Image (optional)</label>
                  <input type="file" name="branch_image" accept="image/*" class="form-control form-control-sm">
                </div>
                <div class="d-grid"><button class="btn btn-success btn-sm">Add Branch</button></div>
              </form>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card card-rounded p-3">
              <h6 class="mb-3">Branches</h6>
              <div class="row g-3">
                <?php if (!empty($branches)): ?>
                  <?php foreach ($branches as $b): ?>
                    <div class="col-md-4">
                      <div class="card h-100">
                        <?php if (!empty($b['image_path'])): ?>
                          <img src="<?= safe($b['image_path']) ?>" class="branch-img" alt="">
                        <?php endif; ?>
                        <div class="card-body text-center">
                          <h6 class="mb-1"><?= safe($b['name']) ?></h6>
                          <small class="text-muted">ID: <?= $b['id'] ?></small>
                          <div class="mt-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-warning btn-branch-edit" data-id="<?= $b['id'] ?>" data-name="<?= safe($b['name']) ?>">Edit</button>

                            <form method="POST" style="display:inline-block" onsubmit="return confirm('Delete branch?');">
                              <?= csrf_field() ?>
                              <input type="hidden" name="action" value="delete_branch">
                              <input type="hidden" name="branch_id" value="<?= $b['id'] ?>">
                              <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="col-12"><p class="text-muted">No branches yet.</p></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Branch edit insertion area (JS will insert) -->
        <div id="branchEditContainer" class="mt-4"></div>
      </section>

      <!-- Courses (Add course + semester quick select) -->
      <section id="coursesSection" class="page-section" style="display:none">
        <div class="card card-rounded p-4 mb-4">
          <h5 class="mb-3">Add Course</h5>
          <form method="POST" action="" enctype="multipart/form-data" id="addCourseForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_course">

            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label small">Scheme</label>
                <select id="scheme_id_course" name="scheme_id" class="form-select form-select-sm" required>
                  <option value="">Select scheme</option>
                  <?php foreach ($schemes as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= safe($s['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label small">Branch</label>
                <select id="branch_id_course" name="branch_id" class="form-select form-select-sm" required>
                  <option value="">Select branch</option>
                  <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= safe($b['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label small">Semester</label>
                <select id="semester_select" name="semester" class="form-select form-select-sm" required>
                  <option value="">Select</option>
                  <?php for ($i=1;$i<=8;$i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>

            <div class="row g-2 mt-3">
              <div class="col-md-8">
                <label class="form-label small">Course name</label>
                <input type="text" name="course_name" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small">Course image (optional)</label>
                <input type="file" name="course_image" accept="image/*" class="form-control form-control-sm">
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label small">Course links</label>
              <div id="links-list-course">
                <div class="row link-row g-2 mb-2">
                  <div class="col">
                    <input type="text" name="links[0][link_name]" class="form-control form-control-sm" placeholder="Link name" required>
                  </div>
                  <div class="col">
                    <input type="url" name="links[0][url]" class="form-control form-control-sm" placeholder="Link URL" required>
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                  </div>
                </div>
              </div>
              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-secondary" id="addLinkCourseBtn">Add link</button>
              </div>
            </div>

            <div class="mt-3 d-grid"><button class="btn btn-primary btn-sm">Add Course</button></div>
          </form>
        </div>

        <!-- <div class="card card-rounded p-3">
          <h6 class="mb-2">Semesters</h6>
          <div class="mb-2 d-flex flex-wrap">
            <?php for ($s=1;$s<=8;$s++): ?>
              <div class="semester-pill" data-sem="<?= $s ?>">Sem <?= $s ?></div>
            <?php endfor; ?>
          </div>
          <small class="text-muted">Click a semester to set it while adding a course above</small>
        </div> -->
      </section>

      <!-- Links (update) -->
      <section id="linksSection" class="page-section" style="display:none">
        <div class="card card-rounded p-3">
          <h6 class="mb-3">Update Course Links</h6>
          <form method="GET" action="" id="selectCourseForLinksForm" class="mb-3">
            <label class="form-label small">Select course</label>
            <select name="course_id" class="form-select form-select-sm" onchange="document.getElementById('selectCourseForLinksForm').submit()">
              <option value="">Select a course</option>
              <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= (isset($_GET['course_id']) && intval($_GET['course_id']) === $course['id']) ? 'selected' : '' ?>>
                  <?= safe($course['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>

          <?php if ($selected_course): ?>
            <form method="POST" action="">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="save_links">
              <input type="hidden" name="course_id" value="<?= $selected_course['id'] ?>">

              <div class="mb-2">
                <label class="form-label small">Course</label>
                <input type="text" class="form-control form-control-sm" value="<?= safe($selected_course['name']) ?>" readonly>
              </div>

              <div id="links-list-update">
                <?php if (!empty($links)): ?>
                  <?php foreach ($links as $idx => $lnk): ?>
                    <div class="row link-row g-2 mb-2">
                      <div class="col">
                        <input type="text" name="links[<?= $idx ?>][link_name]" class="form-control form-control-sm" value="<?= safe($lnk['link_name'] ?? '') ?>" required>
                      </div>
                      <div class="col">
                        <input type="url" name="links[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= safe($lnk['url'] ?? '') ?>" required>
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="row link-row g-2 mb-2">
                    <div class="col">
                      <input type="text" name="links[0][link_name]" class="form-control form-control-sm" placeholder="Link name" required>
                    </div>
                    <div class="col">
                      <input type="url" name="links[0][url]" class="form-control form-control-sm" placeholder="Link URL" required>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-secondary" id="addLinkUpdateBtn">Add link</button>
              </div>

              <div class="mt-3"><button class="btn btn-primary btn-sm">Save Links</button></div>
            </form>
          <?php endif; ?>
        </div>
      </section>

    </main>
  </div>
</div>

<!-- Edit course modal (Bootstrap 5) -->
<div class="modal fade" id="courseEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="courseEditForm" method="POST" action="" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit_course">
        <input type="hidden" name="course_id" id="modal_course_id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label small">Course name</label>
            <input type="text" name="course_name_edit" id="modal_course_name" class="form-control form-control-sm" required>
          </div>
          <div class="mb-2">
            <label class="form-label small">Semester</label>
            <select name="semester_edit" id="modal_course_sem" class="form-select form-select-sm" required>
              <?php for ($i=1;$i<=8;$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Replace image (optional)</label>
            <input type="file" name="course_image_edit" class="form-control form-control-sm">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary btn-sm">Save</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS (no jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  // Elements
  const btnTableMode = document.getElementById('btnTableMode');
  const btnCardMode = document.getElementById('btnCardMode');
  const tableMode = document.getElementById('tableMode');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileCloseSidebarBtn = document.getElementById('mobileCloseSidebarBtn');
  const sidebar = document.getElementById('sidebar');
  const collapseSidebarBtn = document.getElementById('collapseSidebarBtn');

  // Section elements
  const sections = document.querySelectorAll('.page-section');
  const dashboardSection = document.getElementById('dashboardSection');
  const schemesSection = document.getElementById('schemesSection');
  const branchesSection = document.getElementById('branchesSection');
  const coursesSection = document.getElementById('coursesSection');
  const linksSection = document.getElementById('linksSection');

  // Table filters
  const tmFilterScheme = document.getElementById('tm_filter_scheme');
  const tmFilterBranch = document.getElementById('tm_filter_branch');
  const tmFilterSem = document.getElementById('tm_filter_semester');
  const tmSearch = document.getElementById('tm_search');
  const tmTable = document.getElementById('tm_table');

  // Link management counters
  let linkCountCourse = 1;
  let linkCountUpdate = <?= !empty($links) ? count($links) : 1 ?>;

  // Utility: show only one section by id
  function showSection(id){
    sections.forEach(s => s.style.display = (s.id === id ? '' : 'none'));
    // Update active nav-link
    document.querySelectorAll('.nav-link[data-target]').forEach(a => {
      if (a.getAttribute('data-target') === id) a.classList.add('active'); else a.classList.remove('active');
    });
    // collapse mobile sidebar after navigation
    if (window.innerWidth < 992) sidebar.classList.remove('show');
  }

  // Default: show dashboard
  showSection('dashboardSection');

  // Sidebar nav links
  document.querySelectorAll('.nav-link[data-target]').forEach(a=>{
    a.addEventListener('click', function(e){
      e.preventDefault();
      const target = this.getAttribute('data-target');
      if (target) showSection(target);
    });
  });

  // Toggle modes (Table shows dashboard, Card shows courses section)
  function showTableMode(){
    showSection('dashboardSection');
    btnTableMode.classList.remove('btn-outline-secondary');
    btnTableMode.classList.add('btn-primary');
    btnCardMode.classList.remove('btn-primary');
    btnCardMode.classList.add('btn-outline-secondary');
  }
  function showCardMode(){
    showSection('coursesSection');
    btnCardMode.classList.remove('btn-outline-secondary');
    btnCardMode.classList.add('btn-primary');
    btnTableMode.classList.remove('btn-primary');
    btnTableMode.classList.add('btn-outline-secondary');
  }
  btnTableMode && btnTableMode.addEventListener('click', showTableMode);
  btnCardMode && btnCardMode.addEventListener('click', showCardMode);

  // Mobile sidebar toggle
  mobileMenuBtn && mobileMenuBtn.addEventListener('click', function(){
    sidebar.classList.add('show');
  });
  mobileCloseSidebarBtn && mobileCloseSidebarBtn.addEventListener('click', function(){
    sidebar.classList.remove('show');
  });

  // Sidebar collapse toggle (desktop)
  collapseSidebarBtn && collapseSidebarBtn.addEventListener('click', () => {
    document.body.classList.toggle('sidebar-collapsed');
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(e){
    if(window.innerWidth < 992){
      if(!sidebar.contains(e.target) && !mobileMenuBtn.contains(e.target)){
        sidebar.classList.remove('show');
      }
    }
  });

  // Table filter function
  function applyFilters(){
    const scheme = (tmFilterScheme.value || '').toLowerCase();
    const branch = (tmFilterBranch.value || '').toLowerCase();
    const sem = (tmFilterSem.value || '').toLowerCase();
    const search = (tmSearch.value || '').toLowerCase();
    Array.from(tmTable.tBodies[0].rows).forEach(row=>{
      const rowScheme = (row.getAttribute('data-scheme') || '').toLowerCase();
      const rowBranch = (row.getAttribute('data-branch') || '').toLowerCase();
      const rowSem = (row.getAttribute('data-semester') || '').toLowerCase();
      const rowName = (row.getAttribute('data-name') || '').toLowerCase();
      const matches = (scheme === '' || rowScheme === scheme)
                    && (branch === '' || rowBranch === branch)
                    && (sem === '' || rowSem === sem)
                    && (search === '' || rowName.indexOf(search) !== -1);
      row.style.display = matches ? '' : 'none';
    });
  }
  [tmFilterScheme, tmFilterBranch, tmFilterSem, tmSearch].forEach(el=>{
    el && el.addEventListener('change', applyFilters);
    el && el.addEventListener('keyup', applyFilters);
  });

  // Edit course modal (Bootstrap)
  const courseEditModalEl = document.getElementById('courseEditModal');
  const courseEditModal = new bootstrap.Modal(courseEditModalEl);
  document.querySelectorAll('.btn-edit-course').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      const sem = btn.getAttribute('data-sem');
      document.getElementById('modal_course_id').value = id;
      document.getElementById('modal_course_name').value = name;
      document.getElementById('modal_course_sem').value = sem;
      courseEditModal.show();
    });
  });

  // Inline branch edit insertion
  const branchEditContainer = document.getElementById('branchEditContainer');
  document.querySelectorAll('.btn-branch-edit').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      // remove existing
      branchEditContainer.innerHTML = '';
      const formHtml = `
        <div class="card card-rounded p-3 mb-3">
          <form method="POST" action="" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="edit_branch">
            <input type="hidden" name="branch_id" value="${escapeHtml(id)}">
            <div class="mb-2">
              <label class="form-label small">Branch name</label>
              <input type="text" name="branch_name" class="form-control form-control-sm" value="${escapeHtml(name)}" required>
            </div>
            <div class="mb-2">
              <label class="form-label small">Replace image (optional)</label>
              <input type="file" name="branch_image_edit" accept="image/*" class="form-control form-control-sm">
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-sm btn-secondary" id="cancelBranchEdit">Cancel</button>
              <button class="btn btn-sm btn-primary">Save</button>
            </div>
          </form>
        </div>
      `;
      branchEditContainer.innerHTML = formHtml;
      document.getElementById('cancelBranchEdit').addEventListener('click', ()=>{
        branchEditContainer.innerHTML = '';
        branchEditContainer.scrollIntoView({behavior:'smooth'});
      });
      branchEditContainer.scrollIntoView({behavior:'smooth', block:'center'});
    });
  });

  // Semester quick select behavior
  document.querySelectorAll('.semester-pill').forEach(pill=>{
    pill.addEventListener('click', ()=>{
      document.querySelectorAll('.semester-pill').forEach(x=>x.classList.remove('active'));
      pill.classList.add('active');
      const sem = pill.getAttribute('data-sem');
      const semSelect = document.getElementById('semester_select');
      if(semSelect){
        semSelect.value = sem;
        semSelect.scrollIntoView({behavior:'smooth', block:'center'});
      }
    });
  });

  // Links: add/remove for Add Course
  const linksListCourse = document.getElementById('links-list-course');
  const addLinkCourseBtn = document.getElementById('addLinkCourseBtn');
  addLinkCourseBtn && addLinkCourseBtn.addEventListener('click', ()=>{
    const idx = linkCountCourse++;
    const row = document.createElement('div');
    row.className = 'row link-row g-2 mb-2';
    row.innerHTML = `
      <div class="col">
        <input type="text" name="links[${idx}][link_name]" class="form-control form-control-sm" placeholder="Link name" required>
      </div>
      <div class="col">
        <input type="url" name="links[${idx}][url]" class="form-control form-control-sm" placeholder="Link URL" required>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
      </div>
    `;
    linksListCourse.appendChild(row);
    row.querySelector('.btn-remove-link').addEventListener('click', ()=>row.remove());
  });

  // Links for update
  const linksListUpdate = document.getElementById('links-list-update');
  const addLinkUpdateBtn = document.getElementById('addLinkUpdateBtn');
  addLinkUpdateBtn && addLinkUpdateBtn.addEventListener('click', ()=>{
    const idx = linkCountUpdate++;
    const row = document.createElement('div');
    row.className = 'row link-row g-2 mb-2';
    row.innerHTML = `
      <div class="col">
        <input type="text" name="links[${idx}][link_name]" class="form-control form-control-sm" placeholder="Link name" required>
      </div>
      <div class="col">
        <input type="url" name="links[${idx}][url]" class="form-control form-control-sm" placeholder="Link URL" required>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
      </div>
    `;
    linksListUpdate && linksListUpdate.appendChild(row);
    row.querySelector('.btn-remove-link').addEventListener('click', ()=>row.remove());
  });

  // Attach remove listeners for any existing remove buttons (hidden by default)
  document.querySelectorAll('.btn-remove-link').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const row = btn.closest('.link-row');
      row && row.remove();
    });
  });

  // Utility escape for inserted values
  function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return String(unsafe)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }

})();
</script>
</body>
</html>
