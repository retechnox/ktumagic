<?php
include __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

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

function safe($v) { return htmlspecialchars((string)$v, ENT_QUOTES); }

// -----------------------------
// Drive link conversion helper
// -----------------------------
function convertDriveLink(?string $url): ?string
{
    if (!$url) return null;

    $url = trim($url);

    // If already a direct image (non-drive), return as-is
    if (!str_contains($url, 'drive.google.com')) {
        return $url;
    }

    $fileId = null;

    // Format: https://drive.google.com/file/d/FILE_ID/view
    if (preg_match('~/file/d/([a-zA-Z0-9_-]+)~', $url, $m)) {
        $fileId = $m[1];
    }

    // Format: https://drive.google.com/open?id=FILE_ID
    elseif (preg_match('~[?&]id=([a-zA-Z0-9_-]+)~', $url, $m)) {
        $fileId = $m[1];
    }

    // Format: https://drive.google.com/uc?id=FILE_ID
    elseif (preg_match('~/uc\?id=([a-zA-Z0-9_-]+)~', $url, $m)) {
        $fileId = $m[1];
    }

    // If file ID found → return thumbnail
    if ($fileId) {
        return "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000";
    }

    // Fallback (return original if not matched)
    return $url;
}


// -----------------------------
// Actions (POST)
// -----------------------------
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $stmt = $pdo->prepare('DELETE FROM schemes WHERE id = ?');
            $stmt->execute([$id]);
            flash('Scheme deleted.', 'success');
        }

        elseif ($action === 'add_branch') {
            $scheme_id = intval($_POST['scheme_id'] ?? 0);
            $name = trim($_POST['branch_name'] ?? '');
            if (!$scheme_id) throw new Exception('Select a scheme.');
            if ($name === '') throw new Exception('Branch name is required.');

            $rawImage = trim($_POST['branch_image'] ?? '');
            $image = convertDriveLink($rawImage);

            $stmt = $pdo->prepare('INSERT INTO branches (scheme_id, name, image_path) VALUES (?, ?, ?)');
            $stmt->execute([$scheme_id, $name, $image]);
            flash('Branch added.', 'success');
        }

        elseif ($action === 'edit_branch') {
            $branch_id = intval($_POST['branch_id'] ?? 0);
            $branch_name = trim($_POST['branch_name'] ?? '');
            if (!$branch_id) throw new Exception('Invalid branch id.');
            if ($branch_name === '') throw new Exception('Branch name required.');

            $rawImage = trim($_POST['branch_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            if ($image !== null) {
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
            $del = $pdo->prepare('DELETE FROM branches WHERE id = ?');
            $del->execute([$branch_id]);
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
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url];
            }

            $rawImage = trim($_POST['course_image'] ?? '');
            $image = convertDriveLink($rawImage);

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

            $rawImage = trim($_POST['course_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            if ($image !== null) {
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
            $del = $pdo->prepare('DELETE FROM courses WHERE id = ?');
            $del->execute([$course_id]);
            flash('Course deleted.', 'success');
        }

        // IMPORTANT: save_links now updates course name, semester and image_path in addition to links
        elseif ($action === 'save_links') {
            $course_id = intval($_POST['course_id'] ?? 0);
            if (!$course_id) throw new Exception('Invalid course id.');

            // New editable course fields from the links editor
            $course_name = trim($_POST['course_name_edit'] ?? '');
            $semester = intval($_POST['semester_edit'] ?? 0) ?: null;
            $rawImage = trim($_POST['course_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            $links = $_POST['links'] ?? [];
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url];
            }

            // Update links
            $stmt = $pdo->prepare('UPDATE courses SET links = ? WHERE id = ?');
            $stmt->execute([json_encode($validLinks), $course_id]);

            // Update optional course metadata if provided (name/sem/image)
            if ($course_name !== '') {
                if ($image !== null) {
                    $u = $pdo->prepare('UPDATE courses SET name = ?, semester = ?, image_path = ? WHERE id = ?');
                    $u->execute([$course_name, $semester, $image, $course_id]);
                } else {
                    $u = $pdo->prepare('UPDATE courses SET name = ?, semester = ? WHERE id = ?');
                    $u->execute([$course_name, $semester, $course_id]);
                }
            } else {
                // Even if course_name empty (shouldn't happen) still allow image/semester update
                if ($image !== null) {
                    $u = $pdo->prepare('UPDATE courses SET semester = ?, image_path = ? WHERE id = ?');
                    $u->execute([$semester, $image, $course_id]);
                } else {
                    $u = $pdo->prepare('UPDATE courses SET semester = ? WHERE id = ?');
                    $u->execute([$semester, $course_id]);
                }
            }

            flash('Course links and metadata saved.', 'success');
        }

        // redirect after POST to avoid double submit
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

} catch (Exception $e) {
    flash('Error: ' . $e->getMessage(), 'danger');
}

// -----------------------------
// Fetch data for rendering
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

$selected_course = null;
$links = [];
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $q = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $q->execute([$course_id]);
    $selected_course = $q->fetch();
    if ($selected_course) $links = json_decode($selected_course['links'] ?: '[]', true) ?: [];
}

$flashes = flash();
$csrfToken = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES);
?>
<!doctype html>
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
      <a href="#" class="nav-link" data-target="schemesSection"><span class="nav-caption">Schemes</span></a>
      <a href="#" class="nav-link" data-target="branchesSection"><span class="nav-caption">Branches</span></a>
      <a href="#" class="nav-link" data-target="coursesSection"><span class="nav-caption">Courses</span></a>
      <a href="#" class="nav-link active" data-target="linksSection"><span class="nav-caption">Course Links</span></a>
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
        <!-- (intentionally blank) -->
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

      <!-- Schemes Section -->
      <section id="schemesSection" class="page-section" style="display:none">
        <div class="card card-rounded p-3 mb-3">
          <h5 class="mb-3">Add / Manage Schemes</h5>

          <div class="row">
            <div class="col-md-4">
              <form method="POST">
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

      <!-- Branches Section -->
      <section id="branchesSection" class="page-section" style="display:none">
        <div class="row g-3">

          <div class="col-lg-4">
            <div class="card card-rounded p-3">
              <h6 class="mb-3">Add Branch</h6>

              <form method="POST">
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
                  <label class="form-label small">Drive image link (optional)</label>
                  <input type="text" name="branch_image" class="form-control form-control-sm">
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
                          <img referrerpolicy="no-referrer" src="<?= safe($b['image_path'])  ?>" class="branch-img" alt="" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div class="card-body text-center">
                          <h6 class="mb-1"><?= safe($b['name']) ?></h6>
                          <small class="text-muted">ID: <?= $b['id'] ?></small>
                          <div class="mt-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-warning btn-branch-edit"
                              data-id="<?= $b['id'] ?>"
                              data-name="<?= safe($b['name']) ?>">Edit</button>

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

        <div id="branchEditContainer" class="mt-4"></div>
      </section>

      <!-- LINKS SECTION (DEFAULT VISIBLE) -->
      <section id="linksSection" class="page-section">
        <div class="card card-rounded p-3">
          <h6 class="mb-3">Update Course Links</h6>

          <!-- FILTERS -->
          <div class="row g-2 mb-3 align-items-center">
            <div class="col-md-3">
              <label class="form-label small">Scheme</label>
              <select id="filter_scheme" class="form-select form-select-sm">
                <option value="">All schemes</option>
                <?php foreach ($schemes as $s): ?>
                  <option value="<?= safe(strtolower($s['name'])) ?>"><?= safe($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Branch</label>
              <select id="filter_branch" class="form-select form-select-sm">
                <option value="">All branches</option>
                <?php foreach ($branches as $b): ?>
                  <option value="<?= safe(strtolower($b['name'])) ?>"><?= safe($b['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small">Semester</label>
              <select id="filter_sem" class="form-select form-select-sm">
                <option value="">All</option>
                <?php for ($i=1;$i<=8;$i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input id="filter_search" class="form-control form-control-sm" placeholder="Search course...">
            </div>
          </div>

          <!-- SELECT COURSE -->
          <form method="GET" id="selectCourseForLinksForm" class="mb-3">
            <label class="form-label small">Select course</label>
            <select name="course_id" id="course_select_for_links" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Select a course</option>
              <?php foreach ($courses as $course):
                $dScheme = safe(strtolower($course['scheme_name']));
                $dBranch = safe(strtolower($course['branch_name']));
                $dSem = safe($course['semester']);
                $dName = safe(strtolower($course['name']));
              ?>
                <option value="<?= $course['id'] ?>"
                        data-scheme="<?= $dScheme ?>"
                        data-branch="<?= $dBranch ?>"
                        data-sem="<?= $dSem ?>"
                        data-name="<?= $dName ?>"
                        <?= (isset($_GET['course_id']) && intval($_GET['course_id']) === $course['id']) ? 'selected' : '' ?>>
                  <?= safe($course['name']) ?> (<?= safe($course['branch_name']) ?> — Sem <?= safe($course['semester']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </form>

          <?php if ($selected_course): ?>
            <!-- EDITABLE COURSE META + LINKS -->
            <form method="POST">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="save_links">
              <input type="hidden" name="course_id" value="<?= $selected_course['id'] ?>">

              <!-- Editable Course Name -->
              <div class="mb-2">
                <label class="form-label small">Course Name</label>
                <input type="text"
                       name="course_name_edit"
                       class="form-control form-control-sm"
                       value="<?= safe($selected_course['name']) ?>"
                       required>
              </div>

              <!-- Editable Semester -->
              <div class="mb-2">
                <label class="form-label small">Semester</label>
                <select name="semester_edit" class="form-select form-select-sm" required>
                  <?php for ($i=1;$i<=8;$i++): ?>
                    <option value="<?= $i ?>" <?= ($selected_course['semester']==$i?'selected':'') ?>><?= $i ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <!-- Editable Image Path -->
              <div class="mb-2">
                <label class="form-label small">Drive Image Link</label>
                <input type="text"
                       name="course_image_edit"
                       class="form-control form-control-sm"
                       value="<?= safe($selected_course['image_path']) ?>"
                       placeholder="Drive link (optional)">
              </div>

              <!-- Editable Links -->
              <label class="form-label small mt-3">Course Links</label>
              <div id="links-list-update">
                <?php if (!empty($links)): ?>
                  <?php foreach ($links as $idx => $lnk): ?>
                    <div class="row link-row g-2 mb-2">
                      <div class="col">
                        <input type="text" name="links[<?= $idx ?>][link_name]" class="form-control form-control-sm" value="<?= safe($lnk['link_name']) ?>" required>
                      </div>
                      <div class="col">
                        <input type="url" name="links[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= safe($lnk['url']) ?>" required>
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
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
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-secondary" id="addLinkUpdateBtn">Add link</button>
              </div>

              <div class="mt-3">
                <button class="btn btn-primary btn-sm">Save Course</button>
              </div>

            </form>
          <?php endif; ?>

        </div>
      </section>

      <!-- Courses Section -->
      <section id="coursesSection" class="page-section" style="display:none">
        <div class="card card-rounded p-4 mb-4">
          <h5 class="mb-3">Add Course</h5>

          <form method="POST" id="addCourseForm">
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
                <label class="form-label small">Drive image link (optional)</label>
                <input type="text" name="course_image" class="form-control form-control-sm">
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

            <div class="mt-3 d-grid">
              <button class="btn btn-primary btn-sm">Add Course</button>
            </div>

          </form>
        </div>

      </section>
      <!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){

  /* -------------------------
        SECTION HANDLING
  -------------------------- */
  const sections = document.querySelectorAll('.page-section');
  function showSection(id){
    sections.forEach(sec => sec.style.display = (sec.id === id ? '' : 'none'));
    document.querySelectorAll('.nav-link[data-target]').forEach(a=>{
      if(a.dataset.target === id) a.classList.add('active');
      else a.classList.remove('active');
    });
  }

  // default visible = LINKS SECTION
  showSection("linksSection");

  document.querySelectorAll('.nav-link[data-target]').forEach(a=>{
    a.addEventListener('click', e=>{
      e.preventDefault();
      showSection(a.dataset.target);
    });
  });


  /* -------------------------
        MOBILE SIDEBAR
  -------------------------- */
  const sidebar = document.getElementById('sidebar');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileCloseSidebarBtn = document.getElementById('mobileCloseSidebarBtn');
  const collapseSidebarBtn = document.getElementById('collapseSidebarBtn');

  mobileMenuBtn && mobileMenuBtn.addEventListener('click', ()=> sidebar.classList.add('show'));
  mobileCloseSidebarBtn && mobileCloseSidebarBtn.addEventListener('click', ()=> sidebar.classList.remove('show'));
  collapseSidebarBtn && collapseSidebarBtn.addEventListener('click', ()=> document.body.classList.toggle('sidebar-collapsed'));

  document.addEventListener('click', (e)=>{
    if(window.innerWidth < 992){
      if(!sidebar.contains(e.target) && !mobileMenuBtn.contains(e.target)){
        sidebar.classList.remove('show');
      }
    }
  });


  /* -------------------------
      BRANCH EDIT INLINE FORM
  -------------------------- */
  const branchEditContainer = document.getElementById('branchEditContainer');

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-branch-edit');
    if(!btn) return;

    const id = btn.dataset.id;
    const name = btn.dataset.name;

    branchEditContainer.innerHTML = `
      <div class="card card-rounded p-3 mb-3">
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo $csrfToken ?>">
          <input type="hidden" name="action" value="edit_branch">
          <input type="hidden" name="branch_id" value="${id}">

          <div class="mb-2">
            <label class="form-label small">Branch name</label>
            <input type="text" name="branch_name" class="form-control form-control-sm" value="${name}" required>
          </div>

          <div class="mb-2">
            <label class="form-label small">Drive image link (optional)</label>
            <input type="text" name="branch_image_edit" class="form-control form-control-sm">
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-sm btn-secondary" id="cancelBranchEdit">Cancel</button>
            <button class="btn btn-sm btn-primary">Save</button>
          </div>
        </form>
      </div>
    `;

    document.getElementById('cancelBranchEdit').addEventListener('click', ()=>{
      branchEditContainer.innerHTML = '';
    });

    branchEditContainer.scrollIntoView({behavior:"smooth"});
  });


  /* -------------------------
       LINKS FILTER LOGIC
  -------------------------- */
  const filterScheme = document.getElementById("filter_scheme");
  const filterBranch = document.getElementById("filter_branch");
  const filterSem = document.getElementById("filter_sem");
  const filterSearch = document.getElementById("filter_search");
  const courseSelect = document.getElementById("course_select_for_links");

  function applyFilters(){
    const fs = (filterScheme.value || "").toLowerCase();
    const fb = (filterBranch.value || "").toLowerCase();
    const fsem = (filterSem.value || "");
    const fsearch = (filterSearch.value || "").toLowerCase();

    Array.from(courseSelect.options).forEach((opt, idx)=>{
      if(idx === 0) return; // skip first placeholder

      const os = opt.dataset.scheme;
      const ob = opt.dataset.branch;
      const osem = opt.dataset.sem;
      const oname = opt.dataset.name;

      let show = true;
      if(fs && fs !== os) show = false;
      if(fb && fb !== ob) show = false;
      if(fsem && fsem !== osem) show = false;
      if(fsearch && !oname.includes(fsearch)) show = false;

      opt.hidden = !show;
      if(opt.hidden && opt.selected) opt.selected = false;
    });
  }

  [filterScheme, filterBranch, filterSem].forEach(x=>{
    if(x) x.addEventListener('change', applyFilters);
  });
  if(filterSearch) filterSearch.addEventListener('input', applyFilters);

  applyFilters(); // first load


  /* -------------------------
      ADD / REMOVE LINK ROWS
  -------------------------- */
  let linkUpdateCount = <?= !empty($links) ? count($links) : 1 ?>;
  const addLinkUpdateBtn = document.getElementById('addLinkUpdateBtn');
  const linksListUpdate = document.getElementById('links-list-update');

  if(addLinkUpdateBtn){
    addLinkUpdateBtn.addEventListener('click', ()=>{
      const idx = linkUpdateCount++;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
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
      linksListUpdate.appendChild(row);
    });
  }

  // Delegate remove buttons
  document.addEventListener("click", function(e){
    const btn = e.target.closest(".btn-remove-link");
    if(!btn) return;

    const row = btn.closest(".link-row");
    if(row) row.remove();
  });


  /* -------------------------
      EDIT COURSE MODAL
  -------------------------- */
  const courseEditModalEl = document.getElementById("courseEditModal");
  const courseEditModal = new bootstrap.Modal(courseEditModalEl);

  document.querySelectorAll(".btn-edit-course").forEach(btn=>{
    btn.addEventListener("click", ()=>{

      document.getElementById("modal_course_id").value = btn.dataset.id;
      document.getElementById("modal_course_name").value = btn.dataset.name;
      document.getElementById("modal_course_sem").value = btn.dataset.sem;

      courseEditModal.show();
    });
  });

})();
</script>

</body>
</html>


