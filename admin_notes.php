<?php
include __DIR__ . '/db.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

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
            $syllabus = trim($_POST['syllabus_link'] ?? '');
            $calendar = trim($_POST['calendar_link'] ?? '');
            $timetable = trim($_POST['timetable_link'] ?? '');
            $checkOrderCol = $pdo->query("SHOW COLUMNS FROM branches LIKE 'display_order'")->rowCount() > 0;
            $order = $checkOrderCol ? intval($_POST['display_order'] ?? 0) : 0;
            
            // Check for redirect_branch_id column
            $checkRedirectCol = $pdo->query("SHOW COLUMNS FROM branches LIKE 'redirect_branch_id'")->rowCount() > 0;
            if (!$checkRedirectCol) {
                $pdo->exec("ALTER TABLE branches ADD COLUMN redirect_branch_id INT DEFAULT NULL");
            }
            $redirect_id = intval($_POST['redirect_branch_id'] ?? 0) ?: null;

            if (!$scheme_id) throw new Exception('Select a scheme.');
            if ($name === '') throw new Exception('Branch name is required.');

            $rawImage = trim($_POST['branch_image'] ?? '');
            $image = convertDriveLink($rawImage);

            $semester_data = json_encode([]);

            if ($checkOrderCol) {
                $stmt = $pdo->prepare('INSERT INTO branches (scheme_id, name, image_path, syllabus_link, calendar_link, timetable_link, display_order, semester_data, redirect_branch_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$scheme_id, $name, $image, $syllabus, $calendar, $timetable, $order, $semester_data, $redirect_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO branches (scheme_id, name, image_path, syllabus_link, calendar_link, timetable_link, semester_data, redirect_branch_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$scheme_id, $name, $image, $syllabus, $calendar, $timetable, $semester_data, $redirect_id]);
            }
            flash('Branch added.', 'success');
        }

        elseif ($action === 'edit_branch') {
            $branch_id = intval($_POST['branch_id'] ?? 0);
            $branch_name = trim($_POST['branch_name'] ?? '');
            $syllabus = trim($_POST['syllabus_link'] ?? '');
            $calendar = trim($_POST['calendar_link'] ?? '');
            $timetable = trim($_POST['timetable_link'] ?? '');
            
            $sem_data = [];
            if (isset($_POST['semester_links'])) {
                foreach ($_POST['semester_links'] as $sem => $links) {
                    $sem_data[$sem] = [
                        'syllabus' => trim($links['syllabus'] ?? ''),
                        'timetable' => trim($links['timetable'] ?? ''),
                        'calendar' => trim($links['calendar'] ?? ''),
                        'notes' => trim($links['notes'] ?? '')
                    ];
                }
            }
            $semester_data = json_encode($sem_data);
            $redirect_id = intval($_POST['redirect_branch_id'] ?? 0) ?: null;

            // Ensure column exists
            $checkRedirectCol = $pdo->query("SHOW COLUMNS FROM branches LIKE 'redirect_branch_id'")->rowCount() > 0;
            if (!$checkRedirectCol) {
                $pdo->exec("ALTER TABLE branches ADD COLUMN redirect_branch_id INT DEFAULT NULL");
            }

            $checkOrderCol = $pdo->query("SHOW COLUMNS FROM branches LIKE 'display_order'")->rowCount() > 0;
            $order = $checkOrderCol ? intval($_POST['display_order'] ?? 0) : 0;
            if (!$branch_id) throw new Exception('Invalid branch id.');
            if ($branch_name === '') throw new Exception('Branch name required.');

            $rawImage = trim($_POST['branch_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            if ($image !== null) {
                if ($checkOrderCol) {
                    $stmt = $pdo->prepare('UPDATE branches SET name = ?, image_path = ?, syllabus_link = ?, calendar_link = ?, timetable_link = ?, display_order = ?, semester_data = ?, redirect_branch_id = ? WHERE id = ?');
                    $stmt->execute([$branch_name, $image, $syllabus, $calendar, $timetable, $order, $semester_data, $redirect_id, $branch_id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE branches SET name = ?, image_path = ?, syllabus_link = ?, calendar_link = ?, timetable_link = ?, semester_data = ?, redirect_branch_id = ? WHERE id = ?');
                    $stmt->execute([$branch_name, $image, $syllabus, $calendar, $timetable, $semester_data, $redirect_id, $branch_id]);
                }
            } else {
                if ($checkOrderCol) {
                    $stmt = $pdo->prepare('UPDATE branches SET name = ?, syllabus_link = ?, calendar_link = ?, timetable_link = ?, display_order = ?, semester_data = ?, redirect_branch_id = ? WHERE id = ?');
                    $stmt->execute([$branch_name, $syllabus, $calendar, $timetable, $order, $semester_data, $redirect_id, $branch_id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE branches SET name = ?, syllabus_link = ?, calendar_link = ?, timetable_link = ?, semester_data = ?, redirect_branch_id = ? WHERE id = ?');
                    $stmt->execute([$branch_name, $syllabus, $calendar, $timetable, $semester_data, $redirect_id, $branch_id]);
                }
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
            $checkOrderCol = $pdo->query("SHOW COLUMNS FROM courses LIKE 'display_order'")->rowCount() > 0;
            $order = $checkOrderCol ? intval($_POST['display_order'] ?? 0) : 0;
            $check404Col = $pdo->query("SHOW COLUMNS FROM courses LIKE 'is_404_1'")->rowCount() > 0;
            if (!$check404Col) {
                $pdo->exec("ALTER TABLE courses ADD COLUMN is_404_1 TINYINT(1) DEFAULT 0");
            }
            $checkSyllabusCol = $pdo->query("SHOW COLUMNS FROM courses LIKE 'syllabus'")->rowCount() > 0;
            if (!$checkSyllabusCol) {
                $pdo->exec("ALTER TABLE courses ADD COLUMN syllabus TEXT DEFAULT NULL");
            }
            $is_404_1 = isset($_POST['is_404_1']) ? 1 : 0;

            if (!$scheme_id || !$branch_id) throw new Exception('Select scheme and branch.');
            if ($course_name === '') throw new Exception('Course name required.');

            $links = $_POST['links'] ?? [];
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                $ord = intval($l['display_order'] ?? 0);
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url,'display_order'=>$ord];
            }

            $validPyqs = [];
            $pyq_links = $_POST['pyqs'] ?? [];
            foreach ($pyq_links as $p) {
                $pn = trim($p['link_name'] ?? '');
                $url = trim($p['url'] ?? '');
                $ord = intval($p['display_order'] ?? 0);
                if ($pn !== '' && $url !== '') $validPyqs[] = ['link_name'=>$pn,'url'=>$url,'display_order'=>$ord];
            }

            $validSyllabus = [];
            $syllabus_links = $_POST['syllabus'] ?? [];
            foreach ($syllabus_links as $s) {
                $sn = trim($s['link_name'] ?? '');
                $url = trim($s['url'] ?? '');
                $ord = intval($s['display_order'] ?? 0);
                if ($sn !== '' && $url !== '') $validSyllabus[] = ['link_name'=>$sn,'url'=>$url,'display_order'=>$ord];
            }

            $subject_code = strtoupper(trim($_POST['subject_code'] ?? ''));
            $rawImage = trim($_POST['course_image'] ?? '');
            $image = convertDriveLink($rawImage);

            if ($checkOrderCol) {
                $stmt = $pdo->prepare('INSERT INTO courses (branch_id, scheme_id, name, subject_code, links, pyqs, syllabus, image_path, semester, display_order, is_404_1) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$branch_id, $scheme_id, $course_name, $subject_code, json_encode($validLinks), json_encode($validPyqs), json_encode($validSyllabus), $image, $semester, $order, $is_404_1]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO courses (branch_id, scheme_id, name, subject_code, links, pyqs, syllabus, image_path, semester, is_404_1) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$branch_id, $scheme_id, $course_name, $subject_code, json_encode($validLinks), json_encode($validPyqs), json_encode($validSyllabus), $image, $semester, $is_404_1]);
            }
            flash('Course added.', 'success');
        }

        elseif ($action === 'edit_course') {
            $course_id = intval($_POST['course_id'] ?? 0);
            $name = trim($_POST['course_name'] ?? '');
            $code = trim($_POST['subject_code'] ?? '');
            
            // Check for is_404_1 column
            $check404Col = $pdo->query("SHOW COLUMNS FROM courses LIKE 'is_404_1'")->rowCount() > 0;
            if (!$check404Col) {
                $pdo->exec("ALTER TABLE courses ADD COLUMN is_404_1 TINYINT(1) DEFAULT 0");
            }
            $is_404_1 = isset($_POST['is_404_1']) ? 1 : 0;

            if (!$course_id) throw new Exception('Invalid course id.');
            $checkOrderCol = $pdo->query("SHOW COLUMNS FROM courses LIKE 'display_order'")->rowCount() > 0;
            $order = $checkOrderCol ? intval($_POST['display_order'] ?? 0) : 0;

            if ($name === '') throw new Exception('Course name required.');

            $rawImage = trim($_POST['course_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            if ($image !== null) {
                if ($checkOrderCol) {
                    $stmt = $pdo->prepare('UPDATE courses SET name=?, subject_code=?, image_path=?, display_order=?, is_404_1=? WHERE id=?');
                    $stmt->execute([$name, $code, $image, $order, $is_404_1, $course_id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE courses SET name=?, subject_code=?, image_path=?, is_404_1=? WHERE id=?');
                    $stmt->execute([$name, $code, $image, $is_404_1, $course_id]);
                }
            } else {
                if ($checkOrderCol) {
                    $stmt = $pdo->prepare('UPDATE courses SET name=?, subject_code=?, display_order=?, is_404_1=? WHERE id=?');
                    $stmt->execute([$name, $code, $order, $is_404_1, $course_id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE courses SET name=?, subject_code=?, is_404_1=? WHERE id=?');
                    $stmt->execute([$name, $code, $is_404_1, $course_id]);
                }
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

        // save_links now updates course metadata and links
        elseif ($action === 'save_links') {
            $course_id = intval($_POST['course_id'] ?? 0);
            if (!$course_id) throw new Exception('Invalid course id.');

            $is_404_1 = isset($_POST['is_404_1']) ? 1 : 0;
            $check404Col = $pdo->query("SHOW COLUMNS FROM courses LIKE 'is_404_1'")->rowCount() > 0;
            if (!$check404Col) {
                $pdo->exec("ALTER TABLE courses ADD COLUMN is_404_1 TINYINT(1) DEFAULT 0");
            }

            $course_name = trim($_POST['course_name_edit'] ?? '');
            $semester = intval($_POST['semester_edit'] ?? 0) ?: null;
            $checkOrderCol = $pdo->query("SHOW COLUMNS FROM courses LIKE 'display_order'")->rowCount() > 0;
            $order = $checkOrderCol ? intval($_POST['display_order'] ?? 0) : 0;
            $rawImage = trim($_POST['course_image_edit'] ?? '');
            $image = $rawImage !== '' ? convertDriveLink($rawImage) : null;

            $links = $_POST['links'] ?? [];
            $validLinks = [];
            foreach ($links as $l) {
                $ln = trim($l['link_name'] ?? '');
                $url = trim($l['url'] ?? '');
                $ord = intval($l['display_order'] ?? 0);
                if ($ln !== '' && $url !== '') $validLinks[] = ['link_name'=>$ln,'url'=>$url,'display_order'=>$ord];
            }

            $pyq_links = $_POST['pyqs'] ?? [];
            $validPyqs = [];
            foreach ($pyq_links as $p) {
                $pn = trim($p['link_name'] ?? '');
                $url = trim($p['url'] ?? '');
                $ord = intval($p['display_order'] ?? 0);
                if ($pn !== '' && $url !== '') $validPyqs[] = ['link_name'=>$pn,'url'=>$url,'display_order'=>$ord];
            }

            $syllabus_links = $_POST['syllabus'] ?? [];
            $validSyllabus = [];
            foreach ($syllabus_links as $s) {
                $sn = trim($s['link_name'] ?? '');
                $url = trim($s['url'] ?? '');
                $ord = intval($s['display_order'] ?? 0);
                if ($sn !== '' && $url !== '') $validSyllabus[] = ['link_name'=>$sn,'url'=>$url,'display_order'=>$ord];
            }

            $stmt = $pdo->prepare('UPDATE courses SET links = ?, pyqs = ?, syllabus = ?, is_404_1 = ? WHERE id = ?');
            $stmt->execute([json_encode($validLinks), json_encode($validPyqs), json_encode($validSyllabus), $is_404_1, $course_id]);

            if ($course_name !== '') {
                $subject_code = strtoupper(trim($_POST['subject_code_edit'] ?? ''));
                if ($image !== null) {
                    if ($checkOrderCol) {
                        $u = $pdo->prepare('UPDATE courses SET name = ?, subject_code = ?, semester = ?, image_path = ?, display_order = ? WHERE id = ?');
                        $u->execute([$course_name, $subject_code, $semester, $image, $order, $course_id]);
                    } else {
                        $u = $pdo->prepare('UPDATE courses SET name = ?, subject_code = ?, semester = ?, image_path = ? WHERE id = ?');
                        $u->execute([$course_name, $subject_code, $semester, $image, $course_id]);
                    }
                } else {
                    if ($checkOrderCol) {
                        $u = $pdo->prepare('UPDATE courses SET name = ?, subject_code = ?, semester = ?, display_order = ? WHERE id = ?');
                        $u->execute([$course_name, $subject_code, $semester, $order, $course_id]);
                    } else {
                        $u = $pdo->prepare('UPDATE courses SET name = ?, subject_code = ?, semester = ? WHERE id = ?');
                        $u->execute([$course_name, $subject_code, $semester, $course_id]);
                    }
                }
            }

            flash('Course links and metadata saved.', 'success');
        }

        elseif ($action === 'save_contact') {
            $jsonPath = __DIR__ . '/data/data.json';
            $existingData = json_decode(@file_get_contents($jsonPath) ?: '{}', true);
            $existingData['contact']['email']         = trim($_POST['ct_email'] ?? '');
            $existingData['contact']['phone']         = trim($_POST['ct_phone'] ?? '');
            $existingData['contact']['whatsapp_main'] = trim($_POST['ct_whatsapp_main'] ?? '');
            $existingData['contact']['instagram']     = trim($_POST['ct_instagram'] ?? '');
            $existingData['contact']['telegram']      = trim($_POST['ct_telegram'] ?? '');
            $existingData['contact']['youtube']       = trim($_POST['ct_youtube'] ?? '');
            file_put_contents($jsonPath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            flash('Contact settings saved.', 'success');
        }

        // redirect after POST to avoid double submit
        if ($action === 'delete_course') {
            header('Location: admin_notes.php');
        } else {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
        exit;
    }

} catch (Exception $e) {
    flash('Error: ' . $e->getMessage(), 'danger');
}

// -----------------------------
// Fetch data for rendering
// -----------------------------
$schemes = $pdo->query('SELECT * FROM schemes ORDER BY name')->fetchAll();
$checkBranchOrder = $pdo->query("SHOW COLUMNS FROM branches LIKE 'display_order'")->rowCount() > 0;
$branchOrderBy = $checkBranchOrder ? "(display_order = 0 OR display_order IS NULL) ASC, display_order ASC, name ASC" : "name ASC";
$branches = $pdo->query("SELECT * FROM branches ORDER BY $branchOrderBy")->fetchAll();

$checkCourseOrder = $pdo->query("SHOW COLUMNS FROM courses LIKE 'display_order'")->rowCount() > 0;
$courseOrderBy = $checkCourseOrder ? "(c.display_order = 0 OR c.display_order IS NULL) ASC, c.display_order ASC, c.name ASC" : "c.id DESC";
$courses = $pdo->query("
    SELECT c.*, s.name AS scheme_name, b.name AS branch_name
    FROM courses c
    LEFT JOIN schemes s ON s.id = c.scheme_id
    LEFT JOIN branches b ON b.id = c.branch_id
    ORDER BY $courseOrderBy
")->fetchAll();

$selected_course = null;
$links = [];
$pyqs = [];
$sem_res = null;

if (isset($_GET['course_id'])) {
    if (!verify_url_sig(true)) {
        $flashtxt = 'Security Error: Invalid or missing token in URL. Request blocked to prevent tampering.';
        $_SESSION['flash'][] = ['msg'=>$flashtxt,'type'=>'danger'];
        header('Location: admin_notes.php');
        exit;
    }

    $course_id = intval($_GET['course_id']);
    $q = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $q->execute([$course_id]);
    $selected_course = $q->fetch();
    if ($selected_course) {
        $links = json_decode($selected_course['links'] ?: '[]', true) ?: [];
        $pyqs = json_decode($selected_course['pyqs'] ?: '[]', true) ?: [];
        $syllabus = json_decode($selected_course['syllabus'] ?: '[]', true) ?: [];
        
        $bQ = $pdo->prepare('SELECT semester_data FROM branches WHERE id = ?');
        $bQ->execute([$selected_course['branch_id']]);
        $branch_data = $bQ->fetch();
        if ($branch_data) {
            $sem_data = json_decode($branch_data['semester_data'] ?: '{}', true);
            $sem_res = $sem_data[$selected_course['semester']] ?? null;
        }
    }
}

$flashes = flash();
$csrfToken = safe(get_csrf_token());
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

    /* Search bar styling */
    .search-wrapper { position: relative; width: 100%; }
    .search-wrapper::before {
      content: '🔍';
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      color: #aaa;
      pointer-events: none;
    }
    #filter_search {
      padding-left: 38px;
      height: 42px;
      font-size: 0.95rem;
      border: 2px solid #e9f2ff;
      border-radius: 10px;
      transition: all 0.2s;
    }
    #filter_search:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }
    #search_suggestions {
      top: 102%;
      border-color: #e9f2ff;
    }

    /* Suggestions Dropdown */
    #search_suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      z-index: 1000;
      max-height: 300px;
      overflow-y: auto;
      background: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: none;
    }
    .suggestion-item {
      padding: 10px 15px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
    }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover { background: #f8fbff; color: #0d6efd; }
    .suggestion-item .course-name { font-weight: bold; display: block; }
    .suggestion-item .course-meta { font-size: 0.75rem; color: #6c757d; }

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
      <a href="#" class="nav-link" data-target="contactSection"><span class="nav-caption">Contact Settings</span></a>
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
        <button class="btn btn-primary btn-sm" onclick="showSection('coursesSection')">+ Add New Course</button>
      </div>
    </header>

    <main class="p-4">
      <?php if (!$checkBranchOrder || !$checkCourseOrder): ?>
        <div class="alert alert-warning card-rounded shadow-sm mb-4">
          <div class="d-flex align-items-center gap-3">
            <div class="fs-4">⚠️</div>
            <div>
              <h6 class="mb-1 fw-bold">Database Migration Required</h6>
              <p class="mb-0 small">The <code>display_order</code> columns are missing from your database. Custom ordering will not work until you run the migration.</p>
              <a href="migrate_order_field.php" class="btn btn-sm btn-warning mt-2 fw-bold">Run Migration Now</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

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

                <!-- Legacy branch resources removed -->

                <?php if ($checkBranchOrder): ?>
                <div class="mb-2">
                  <label class="form-label small">Display Order (optional)</label>
                  <input type="number" name="display_order" class="form-control form-control-sm" value="0">
                </div>
                <?php endif; ?>

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
                        <?php 
                          $img_preview = !empty($b['image_path']) ? $b['image_path'] : "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80";
                        ?>
                        <img referrerpolicy="no-referrer" src="<?= safe($img_preview) ?>" class="branch-img" alt="" onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80'">
                        <div class="card-body text-center">
                          <h6 class="mb-1"><?= safe($b['name']) ?></h6>
                          <small class="text-muted">ID: <?= $b['id'] ?></small>
                          <div class="mt-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-warning btn-branch-edit"
                              data-id="<?= $b['id'] ?>"
                              data-name="<?= safe($b['name']) ?>"
                              data-image="<?= safe($b['image_path'] ?? '') ?>"
                              data-syllabus="<?= safe($b['syllabus_link'] ?? '') ?>"
                              data-calendar="<?= safe($b['calendar_link'] ?? '') ?>"
                              data-timetable="<?= safe($b['timetable_link'] ?? '') ?>"
                              data-order="<?= safe($b['display_order'] ?? 0) ?>"
                              data-redirect="<?= safe($b['redirect_branch_id'] ?? '') ?>">Edit</button>

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
                <?php foreach ($branches as $b): 
                    $scheme_name_for_filter = "";
                    foreach($schemes as $s) if($s['id'] == $b['scheme_id']) { $scheme_name_for_filter = strtolower($s['name']); break; }
                ?>
                  <option value="<?= safe(strtolower($b['name'])) ?>" data-scheme-name="<?= safe($scheme_name_for_filter) ?>"><?= safe($b['name']) ?></option>
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
              <label class="form-label small fw-bold text-primary mb-1">Quick Search</label>
              <div class="search-wrapper">
                <input id="filter_search" class="form-control" placeholder="Search by name or code..." autocomplete="off">
                <div id="search_suggestions"></div>
              </div>
            </div>
          </div>

          <!-- SELECT COURSE -->
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-end mb-1">
              <label class="form-label small mb-0">Select course</label>
              <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" onclick="showSection('coursesSection')">+ Add New Course</button>
            </div>
            <select id="course_select_for_links" class="form-select form-select-sm" onchange="location.href=this.value">
              <option value="">Select a course</option>
              <?php foreach ($courses as $course):
                $dScheme = safe(strtolower($course['scheme_name']));
                $dBranch = safe(strtolower($course['branch_name']));
                $dSem = safe($course['semester']);
                $dName = safe(strtolower($course['name']));
                $signedUrl = sign_url('admin_notes.php', ['course_id' => $course['id']], true);
              ?>
                <option value="<?= $signedUrl ?>"
                        data-scheme="<?= $dScheme ?>"
                        data-branch="<?= $dBranch ?>"
                        data-sem="<?= $dSem ?>"
                        data-name="<?= $dName ?>"
                        <?= (isset($_GET['course_id']) && intval($_GET['course_id']) === $course['id']) ? 'selected' : '' ?>>
                  <?= safe($course['name']) ?> (<?= safe($course['branch_name']) ?> — Sem <?= safe($course['semester']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php if ($selected_course): ?>
            <!-- EDITABLE COURSE META + LINKS -->
            <form id="editCourseForm" method="POST">
              <?= csrf_field() ?>
              <input type="hidden" name="action" id="course_edit_action" value="save_links">
              <input type="hidden" name="course_id" value="<?= $selected_course['id'] ?>">

              <!-- Editable Course Name & Code -->
              <div class="row g-2 mb-2">
                <div class="col-md-8">
                  <label class="form-label small">Course Name</label>
                  <input type="text" name="course_name_edit" class="form-control form-control-sm" value="<?= safe($selected_course['name']) ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Subject Code</label>
                  <input type="text" name="subject_code_edit" class="form-control form-control-sm" value="<?= safe($selected_course['subject_code']) ?>" placeholder="MAT101">
                </div>
              </div>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_404_1" id="is_404_1_check" <?= ($selected_course['is_404_1'] ? 'checked' : '') ?>>
                  <label class="form-check-label small" for="is_404_1_check">
                    Redirect to specialized 404_1 page (with Contribute button) if resources are 404
                  </label>
                </div>
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

              <!-- Semester resources removed (moved to branch) -->

              <!-- Editable Links -->
              <label class="form-label small mt-3">Course Links</label>
              <div id="links-list-update">
                <?php if (!empty($links)): ?>
                  <?php foreach ($links as $idx => $lnk): ?>
                    <div class="row link-row g-2 mb-2">
                      <div class="col-md-5">
                        <input type="text" name="links[<?= $idx ?>][link_name]" class="form-control form-control-sm" value="<?= safe($lnk['link_name']) ?>">
                      </div>
                      <div class="col-md-5">
                        <input type="url" name="links[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= safe($lnk['url']) ?>">
                      </div>
                      <div class="col-md-1">
                        <input type="number" name="links[<?= $idx ?>][display_order]" class="form-control form-control-sm" value="<?= safe($lnk['display_order'] ?? 0) ?>" title="Order">
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="row link-row g-2 mb-2">
                    <div class="col-md-5">
                      <input type="text" name="links[0][link_name]" class="form-control form-control-sm" placeholder="Link name">
                    </div>
                    <div class="col-md-5">
                      <input type="url" name="links[0][url]" class="form-control form-control-sm" placeholder="Link URL">
                    </div>
                    <div class="col-md-1">
                      <input type="number" name="links[0][display_order]" class="form-control form-control-sm" value="0" title="Order">
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

              <!-- Editable PYQ Links -->
              <label class="form-label small mt-4">PYQ Links (Previous Year Questions)</label>
              <div id="pyqs-list-update">
                <?php if (!empty($pyqs)): ?>
                  <?php foreach ($pyqs as $idx => $p): ?>
                    <div class="row link-row g-2 mb-2">
                      <div class="col-md-5">
                        <input type="text" name="pyqs[<?= $idx ?>][link_name]" class="form-control form-control-sm" value="<?= safe($p['link_name']) ?>">
                      </div>
                      <div class="col-md-5">
                        <input type="url" name="pyqs[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= safe($p['url']) ?>">
                      </div>
                      <div class="col-md-1">
                        <input type="number" name="pyqs[<?= $idx ?>][display_order]" class="form-control form-control-sm" value="<?= safe($p['display_order'] ?? 0) ?>" title="Order">
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="row link-row g-2 mb-2">
                    <div class="col">
                      <input type="text" name="pyqs[0][link_name]" class="form-control form-control-sm" placeholder="PYQ Link name">
                    </div>
                    <div class="col">
                      <input type="url" name="pyqs[0][url]" class="form-control form-control-sm" placeholder="PYQ URL">
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-secondary" id="addPyqUpdateBtn">Add PYQ</button>
              </div>

              <!-- SYLLABUS LINKS FOR UPDATE -->
              <label class="form-label small mt-3">Syllabus Links (optional)</label>
              <div id="syllabus-list-update">
                <?php if (!empty($syllabus)): ?>
                  <?php foreach ($syllabus as $idx => $s): ?>
                    <div class="row link-row g-2 mb-2">
                      <div class="col-md-5">
                        <input type="text" name="syllabus[<?= $idx ?>][link_name]" class="form-control form-control-sm" value="<?= safe($s['link_name']) ?>">
                      </div>
                      <div class="col-md-5">
                        <input type="url" name="syllabus[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= safe($s['url']) ?>">
                      </div>
                      <div class="col-md-1">
                        <input type="number" name="syllabus[<?= $idx ?>][display_order]" class="form-control form-control-sm" value="<?= safe($s['display_order'] ?? 0) ?>" title="Order">
                      </div>
                      <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="row link-row g-2 mb-2">
                    <div class="col">
                      <input type="text" name="syllabus[0][link_name]" class="form-control form-control-sm" placeholder="Syllabus Link name">
                    </div>
                    <div class="col">
                      <input type="url" name="syllabus[0][url]" class="form-control form-control-sm" placeholder="Syllabus URL">
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-secondary" id="addSyllabusUpdateBtn">Add Syllabus</button>
              </div>

              <div class="mt-4 d-grid">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>

            </form>

            <div class="mt-4 p-3 border-top bg-light rounded shadow-sm">
                <h6 class="text-danger small mb-2 fw-bold">Danger Zone</h6>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCourseModal">
                    Delete Entire Course
                </button>
                <p class="text-muted x-small mt-2 mb-0">This will delete all links, PYQs, and metadata associated with this course. This action cannot be undone.</p>
            </div>

            <!-- Delete Course Modal -->
            <div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteCourseModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to PERMANENTLY delete the course:</p>
                    <p class="fw-bold fs-5 text-dark"><?= safe($selected_course['name']) ?></p>
                    <div class="alert alert-warning py-2 mb-0 small">
                        ⚠️ This will remove all associated links and documents. This action cannot be undone.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_course">
                        <input type="hidden" name="course_id" value="<?= $selected_course['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete Course</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
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
                    <option value="<?= $b['id'] ?>" data-scheme-id="<?= $b['scheme_id'] ?>"><?= safe($b['name']) ?></option>
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
              <div class="col-md-4">
                <label class="form-label small">Course name</label>
                <input type="text" name="course_name" class="form-control form-control-sm" required placeholder="Calculus">
              </div>

              <div class="col-md-4">
                <label class="form-label small">Subject Code</label>
                <input type="text" name="subject_code" class="form-control form-control-sm" placeholder="e.g. MAT101">
              </div>

              <div class="col-md-3">
                <label class="form-label small">Drive image link (optional)</label>
                <input type="text" name="course_image" class="form-control form-control-sm">
              </div>

            </div>

            <div class="mt-3">
              <label class="form-label small">Course links</label>
              <div id="links-list-course">
                <div class="row link-row g-2 mb-2">
                  <div class="col-md-5">
                    <input type="text" name="links[0][link_name]" class="form-control form-control-sm" placeholder="Link name">
                  </div>
                  <div class="col-md-5">
                    <input type="url" name="links[0][url]" class="form-control form-control-sm" placeholder="Link URL">
                  </div>
                  <div class="col-md-1">
                    <input type="number" name="links[0][display_order]" class="form-control form-control-sm" value="0" title="Order">
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

            <!-- PYQ LINKS FOR ADD -->
            <div class="mt-3">
              <label class="form-label small">PYQ links (optional)</label>
              <div id="pyqs-list-course">
                <div class="row link-row g-2 mb-2">
                  <div class="col-md-5">
                    <input type="text" name="pyqs[0][link_name]" class="form-control form-control-sm" placeholder="PYQ name">
                  </div>
                  <div class="col-md-5">
                    <input type="url" name="pyqs[0][url]" class="form-control form-control-sm" placeholder="PYQ URL">
                  </div>
                  <div class="col-md-1">
                    <input type="number" name="pyqs[0][display_order]" class="form-control form-control-sm" value="0" title="Order">
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                  </div>
                </div>
              </div>

              <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-secondary" id="addPyqCourseBtn">Add PYQ</button>
              </div>
            </div>

            <!-- SYLLABUS LINKS FOR ADD -->
            <div class="mt-3">
              <label class="form-label small">Syllabus links (optional)</label>
              <div id="syllabus-list-course">
                <div class="row link-row g-2 mb-2">
                  <div class="col-md-5">
                    <input type="text" name="syllabus[0][link_name]" class="form-control form-control-sm" placeholder="Syllabus Link name">
                  </div>
                  <div class="col-md-5">
                    <input type="url" name="syllabus[0][url]" class="form-control form-control-sm" placeholder="Syllabus URL">
                  </div>
                  <div class="col-md-1">
                    <input type="number" name="syllabus[0][display_order]" class="form-control form-control-sm" value="0" title="Order">
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link" style="display:none">✕</button>
                  </div>
                </div>
              </div>

              <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-secondary" id="addSyllabusCourseBtn">Add Syllabus</button>
              </div>
            </div>

            <div class="mt-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_404_1" id="add_is_404_1" value="1">
                <label class="form-check-label small" for="add_is_404_1">
                  Redirect to specialized 404_1 page (with Contribute button) if resources are 404
                </label>
              </div>
            </div>

            <div class="mt-3 d-grid">
              <button class="btn btn-primary btn-sm">Add Course</button>
            </div>

          </form>
        </div>

      </section>

      <!-- Contact Settings Section -->
      <?php
        $ctJsonData = @file_get_contents(__DIR__ . '/data/data.json') ?: '{}';
        $ctData = json_decode($ctJsonData, true);
        $ct = $ctData['contact'] ?? [];
      ?>
      <section id="contactSection" class="page-section" style="display:none">
        <div class="card card-rounded p-4 mb-4">
          <h5 class="mb-1">Contact &amp; Social Links</h5>
          <p class="text-muted small mb-3">These values are used across the website — contact page, footer, floating buttons, etc.</p>

          <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_contact">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">📧 Email Address</label>
                <input type="email" name="ct_email" class="form-control form-control-sm"
                       value="<?= safe($ct['email'] ?? '') ?>" placeholder="support@ktumagic.in">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">📞 Phone Number</label>
                <input type="text" name="ct_phone" class="form-control form-control-sm"
                       value="<?= safe($ct['phone'] ?? '') ?>" placeholder="+91 XXXXX XXXXX">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">💬 WhatsApp Main Link</label>
                <input type="url" name="ct_whatsapp_main" class="form-control form-control-sm"
                       value="<?= safe($ct['whatsapp_main'] ?? '') ?>" placeholder="https://wa.me/91...">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">📸 Instagram URL</label>
                <input type="url" name="ct_instagram" class="form-control form-control-sm"
                       value="<?= safe($ct['instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">✈️ Telegram URL</label>
                <input type="url" name="ct_telegram" class="form-control form-control-sm"
                       value="<?= safe($ct['telegram'] ?? '') ?>" placeholder="https://t.me/...">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">▶️ YouTube URL</label>
                <input type="url" name="ct_youtube" class="form-control form-control-sm"
                       value="<?= safe($ct['youtube'] ?? '') ?>" placeholder="https://youtube.com/@...">
              </div>
            </div>

            <div class="mt-4">
              <button class="btn btn-primary btn-sm">Save Contact Settings</button>
            </div>
          </form>
        </div>
      </section>

      <!-- Hidden Forms Removed -->

      <!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  /* -------------------------
        GLOBAL FUNCTIONS
  -------------------------- */

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
    const image = btn.dataset.image || '';
    const syllabus = btn.dataset.syllabus || '';
    const calendar = btn.dataset.calendar || '';
    const timetable = btn.dataset.timetable || '';
    const order = btn.dataset.order || '0';
    const redirect_id = btn.dataset.redirect || '';
    const semesterDataRaw = btn.dataset.semesterData || '{}';
    let semesterData = {};
    try { semesterData = JSON.parse(semesterDataRaw); } catch(e) { semesterData = {}; }
    const checkOrder = <?= $checkBranchOrder ? 'true' : 'false' ?>;

    const orderField = checkOrder ? `
      <div class="mb-2">
        <label class="form-label small">Display Order (optional)</label>
        <input type="number" name="display_order" class="form-control form-control-sm" value="${order}">
      </div>
    ` : '';

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

          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label class="form-label small">Drive image link (optional)</label>
              <input type="text" name="branch_image_edit" class="form-control form-control-sm" value="${image}">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Syllabus Link (Global)</label>
              <input type="url" name="syllabus_link" class="form-control form-control-sm" value="${syllabus}">
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label class="form-label small">Calendar Link</label>
              <input type="url" name="calendar_link" class="form-control form-control-sm" value="${calendar}">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Timetable Link</label>
              <input type="url" name="timetable_link" class="form-control form-control-sm" value="${timetable}">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small">Display Order</label>
              <input type="number" name="display_order" class="form-control form-control-sm" value="${order}">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Redirect to Branch ID (optional)</label>
              <input type="number" name="redirect_branch_id" class="form-control form-control-sm" value="${redirect_id}">
            </div>
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

    // Legacy semester population removed

    branchEditContainer.scrollIntoView({behavior:"smooth"});
  });


  /* -------------------------
       LINKS FILTER LOGIC
  -------------------------- */
  const filterScheme = document.getElementById("filter_scheme");
  const filterBranch = document.getElementById("filter_branch");
  const filterSem = document.getElementById("filter_sem");
  const filterSearch = document.getElementById("filter_search");
  const suggestionsBox = document.getElementById("search_suggestions");
  const courseSelect = document.getElementById("course_select_for_links");

  function applyFilters(){
    const fs = (filterScheme.value || "").toLowerCase();

    // First, filter the branch dropdown based on selected scheme
    const branchOpts = Array.from(filterBranch.options);
    let currentBranchValid = false;
    branchOpts.forEach((opt, idx) => {
        if(idx === 0) return;
        const os = opt.dataset.schemeName;
        const showBranch = !fs || fs === os;
        opt.hidden = !showBranch;
        if(showBranch && opt.selected) currentBranchValid = true;
    });
    if(fs && !currentBranchValid) filterBranch.value = "";

    const fb = (filterBranch.value || "").toLowerCase();
    const fsem = (filterSem.value || "");
    const fsearch = (filterSearch.value || "").toLowerCase();

    const matches = [];

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

      if(show && fsearch.length > 0) {
        matches.push({
          name: opt.text,
          url: opt.value
        });
      }
    });

    renderSuggestions(matches);
  }

  // Also implement filtering for "Add Course" form
  const addCourseScheme = document.getElementById("scheme_id_course");
  const addCourseBranch = document.getElementById("branch_id_course");
  if(addCourseScheme && addCourseBranch) {
      const addBranchOpts = Array.from(addCourseBranch.options);
      addCourseScheme.addEventListener('change', function() {
          const sid = this.value;
          let valid = false;
          addBranchOpts.forEach((opt, idx) => {
              if(idx === 0) return;
              const osid = opt.dataset.schemeId;
              const show = !sid || sid === osid;
              opt.hidden = !show;
              if(show && opt.selected) valid = true;
          });
          if(sid && !valid) addCourseBranch.value = "";
      });
  }

  function renderSuggestions(matches) {
    if (matches.length === 0) {
      suggestionsBox.style.display = 'none';
      return;
    }

    suggestionsBox.innerHTML = '';
    matches.slice(0, 10).forEach(match => {
      const div = document.createElement('div');
      div.className = 'suggestion-item';
      div.innerHTML = `<span class="course-name">${match.name}</span>`;
      div.onclick = () => {
        window.location.href = match.url;
      };
      suggestionsBox.appendChild(div);
    });
    suggestionsBox.style.display = 'block';
  }

  // Close suggestions when clicking outside
  document.addEventListener('click', (e) => {
    if (!filterSearch.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });

  [filterScheme, filterBranch, filterSem].forEach(x=>{
    if(x) x.addEventListener('change', applyFilters);
  });
  if(filterSearch) {
    filterSearch.addEventListener('input', applyFilters);
    filterSearch.addEventListener('focus', () => {
      if(filterSearch.value.length > 0) applyFilters();
    });
  }

  applyFilters(); // first load


  /* -------------------------
      ADD / REMOVE LINK ROWS (Using Delegation)
  -------------------------- */
  let linkUpdateCount = <?= !empty($links) ? count($links) : 1 ?>;
  let pyqUpdateCount = <?= !empty($pyqs) ? count($pyqs) : 1 ?>;
  let syllabusUpdateCount = <?= !empty($syllabus) ? count($syllabus) : 1 ?>;
  let linkAddCount = 1;
  let pyqAddCount = 1;
  let syllabusAddCount = 1;

  document.addEventListener('click', (e) => {
    // Add Link Update
    if (e.target.id === 'addLinkUpdateBtn') {
      const idx = linkUpdateCount++;
      const linksListUpdate = document.getElementById('links-list-update');
      if (!linksListUpdate) return;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
      row.innerHTML = `
        <div class="col-md-5">
          <input type="text" name="links[${idx}][link_name]" class="form-control form-control-sm" placeholder="Link name">
        </div>
        <div class="col-md-5">
          <input type="url" name="links[${idx}][url]" class="form-control form-control-sm" placeholder="Link URL">
        </div>
        <div class="col-md-1">
          <input type="number" name="links[${idx}][display_order]" class="form-control form-control-sm" value="0" title="Order">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
        </div>
      `;
      linksListUpdate.appendChild(row);
    }

    // Add PYQ Update
    if (e.target.id === 'addPyqUpdateBtn') {
      const idx = pyqUpdateCount++;
      const pyqsListUpdate = document.getElementById('pyqs-list-update');
      if (!pyqsListUpdate) return;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
      row.innerHTML = `
        <div class="col-md-5">
          <input type="text" name="pyqs[${idx}][link_name]" class="form-control form-control-sm" placeholder="PYQ name">
        </div>
        <div class="col-md-5">
          <input type="url" name="pyqs[${idx}][url]" class="form-control form-control-sm" placeholder="PYQ URL">
        </div>
        <div class="col-md-1">
          <input type="number" name="pyqs[${idx}][display_order]" class="form-control form-control-sm" value="0" title="Order">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
        </div>
      `;
      pyqsListUpdate.appendChild(row);
    }

    // Add Syllabus Update
    if (e.target.id === 'addSyllabusUpdateBtn') {
      const idx = syllabusUpdateCount++;
      const syllabusListUpdate = document.getElementById('syllabus-list-update');
      if (!syllabusListUpdate) return;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
      row.innerHTML = `
        <div class="col-md-5">
          <input type="text" name="syllabus[${idx}][link_name]" class="form-control form-control-sm" placeholder="Syllabus Link name">
        </div>
        <div class="col-md-5">
          <input type="url" name="syllabus[${idx}][url]" class="form-control form-control-sm" placeholder="Syllabus URL">
        </div>
        <div class="col-md-1">
          <input type="number" name="syllabus[${idx}][display_order]" class="form-control form-control-sm" value="0" title="Order">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
        </div>
      `;
      syllabusListUpdate.appendChild(row);
    }

    // Add Link Course
    if (e.target.id === 'addLinkCourseBtn') {
      const idx = linkAddCount++;
      const linksListCourse = document.getElementById('links-list-course');
      if (!linksListCourse) return;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
      row.innerHTML = `
        <div class="col">
          <input type="text" name="links[${idx}][link_name]" class="form-control form-control-sm" placeholder="Link name">
        </div>
        <div class="col">
          <input type="url" name="links[${idx}][url]" class="form-control form-control-sm" placeholder="Link URL">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
        </div>
      `;
      linksListCourse.appendChild(row);
    }

    // Add Syllabus Course
    if (e.target.id === 'addSyllabusCourseBtn') {
      const idx = syllabusAddCount++;
      const syllabusListCourse = document.getElementById('syllabus-list-course');
      if (!syllabusListCourse) return;
      const row = document.createElement('div');
      row.className = "row link-row g-2 mb-2";
      row.innerHTML = `
        <div class="col-md-5">
          <input type="text" name="syllabus[${idx}][link_name]" class="form-control form-control-sm" placeholder="Syllabus Link name">
        </div>
        <div class="col-md-5">
          <input type="url" name="syllabus[${idx}][url]" class="form-control form-control-sm" placeholder="Syllabus URL">
        </div>
        <div class="col-md-1">
          <input type="number" name="syllabus[${idx}][display_order]" class="form-control form-control-sm" value="0" title="Order">
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link">✕</button>
        </div>
      `;
      syllabusListCourse.appendChild(row);
    }
  });


  // Delegate remove buttons
  document.addEventListener("click", function(e){
    const btn = e.target.closest(".btn-remove-link");
    if(!btn) return;

    const row = btn.closest(".link-row");
    if(row) row.remove();
  });


  /* -------------------------
      EDIT COURSE MODAL (Safety Wrap)
  -------------------------- */
  const courseEditModalEl = document.getElementById("courseEditModal");
  if (courseEditModalEl) {
    const courseEditModal = new bootstrap.Modal(courseEditModalEl);

    document.querySelectorAll(".btn-edit-course").forEach(btn=>{
      btn.addEventListener("click", ()=>{
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const code = btn.dataset.code;
        const semester = btn.dataset.semester;
        const is_404_1 = btn.dataset.is404_1 === '1';

        document.getElementById('course_id_edit').value = id;
        document.getElementById('course_name_edit').value = name;
        document.getElementById('subject_code_edit').value = code;
        document.getElementById('course_is_404_1_edit').checked = is_404_1;
        document.getElementById('semester_edit').value = semester;
        courseEditModal.show();
      });
    });
  }

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
      const btn = this.querySelector('button[type="submit"]');
      if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
      }
    });
  });


</script>

</body>
</html>


