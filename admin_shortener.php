<?php
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

/**
 * Clean internal URLs: remove existing 'sig' parameter
 */
function clean_internal_url($url) {
    $parsed = parse_url($url);
    if (!isset($parsed['query'])) return $url;
    
    parse_str($parsed['query'], $query_params);
    unset($query_params['sig']);
    
    $new_query = http_build_query($query_params);
    $clean_url = $parsed['path'];
    if ($new_query) {
        $clean_url .= '?' . $new_query;
    }
    return $clean_url;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $slug = trim($_POST['slug'] ?? '');
        $dest = trim($_POST['destination'] ?? '');

        // Security Hardening: Slug validation (alphanumeric, dashes, underscores only)
        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $slug)) {
            flash("Invalid slug. Use only letters, numbers, dashes, and underscores.", "danger");
        } 
        elseif (!$slug || !$dest) {
            flash("Slug and Destination are required.", "danger");
        } 
        else {
            // Security Hardening: Destination URL validation
            $dest = filter_var($dest, FILTER_SANITIZE_URL);
            $parsed_dest = parse_url($dest);
            $scheme = strtolower($parsed_dest['scheme'] ?? '');
            
            // Allow only http, https, or relative paths
            if ($scheme !== '' && !in_array($scheme, ['http', 'https'])) {
                flash("Security Error: Invalid protocol. Only http and https are allowed.", "danger");
            } else {
                // Clean the destination URL if it contains a signature
                $dest = clean_internal_url($dest);
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO short_links (slug, destination_url) VALUES (?, ?)");
                    $stmt->execute([$slug, $dest]);
                    flash("Short link created: <strong>/s/$slug</strong>", "success");
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        flash("Slug '$slug' already exists.", "danger");
                    } else {
                        flash("Database error: " . $e->getMessage(), "danger");
                    }
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM short_links WHERE id = ?");
        $stmt->execute([$id]);
        flash("Link deleted.", "info");
    }
    
    header("Location: admin_shortener.php");
    exit();
}

$links = $pdo->query("SELECT * FROM short_links ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Shortener - Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .container { margin-top: 30px; }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table thead th { border-top: none; }
        .slug-preview { font-family: monospace; color: #d63384; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">KTU Magic Admin</a>
            <div class="ml-auto">
                <a href="admin.php" class="btn btn-outline-light btn-sm mr-2">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Link Shortener</h2>

        <?php if ($flashes = flash()): ?>
            <?php foreach ($flashes as $f): ?>
                <div class="alert alert-<?= $f['type'] ?> alert-dismissible fade show">
                    <?= $f['msg'] ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="card-title">Create New Link</h5>
                    <form method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Slug (the part after /s/)</label>
                            <input type="text" name="slug" class="form-control" placeholder="e.g. math-notes" required>
                        </div>
                        <div class="form-group">
                            <label>Destination URL</label>
                            <input type="text" name="destination" class="form-control" placeholder="Paste internal or external URL" required>
                            <small class="form-text text-muted">Signatures will be auto-stripped/re-vaildated.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Shorten Link</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-0 overflow-hidden">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Short URL</th>
                                <th>Destination</th>
                                <th>Created</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $protocol = "http://";
                            if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
                                $protocol = "https://";
                            }
                            $host = $_SERVER['HTTP_HOST'];
                            $base_url = $protocol . $host . "/s/";
                            
                            foreach ($links as $l): 
                                $full_short_url = $base_url . safe($l['slug']);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="slug-preview mr-2"><?= $full_short_url ?></span>
                                        <button class="btn btn-sm btn-light border py-0 px-2" onclick="copyToClipboard('<?= $full_short_url ?>', this)" title="Copy Link">
                                            <small>Copy</small>
                                        </button>
                                    </div>
                                </td>
                                <td class="small text-muted" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= safe($l['destination_url']) ?>">
                                    <?= safe($l['destination_url']) ?>
                                </td>
                                <td class="small"><?= date('M j, Y', strtotime($l['created_at'])) ?></td>
                                <td class="text-right">
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this link?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($links)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No shortened links yet.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<small class="text-success">Copied!</small>';
                btn.classList.add('border-success');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('border-success');
                }, 1500);
            });
        }
    </script>
</body>
</html>
