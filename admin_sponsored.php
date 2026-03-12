<?php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = "";

// Handle file upload
if (isset($_POST['upload'])) {
    try {
        check_csrf();
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/sponsored/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $targetPath = $uploadDir . time() . '_' . $fileName;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                $stmt = $pdo->prepare("INSERT INTO sponsored_images (image_path) VALUES (?)");
                $stmt->execute([$targetPath]);
            }
        }
        $message = "Images uploaded successfully!";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle visibility toggle
if (isset($_GET['toggle'])) {
    if (!verify_url_sig()) die("Security Error: Invalid signature.");
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE sponsored_images SET is_visible = 1 - is_visible WHERE id = ?");
    $stmt->execute([$id]);
}

// Handle delete
if (isset($_GET['delete'])) {
    if (!verify_url_sig()) die("Security Error: Invalid signature.");
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM sponsored_images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();
    if ($image) {
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
        $stmt = $pdo->prepare("DELETE FROM sponsored_images WHERE id = ?");
        $stmt->execute([$id]);
    }
}

$images = $pdo->query("SELECT * FROM sponsored_images ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sponsored Images - KTU Magic Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .image-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
        .table img { max-width: 150px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="admin.php">KTU Magic Admin</a>
        <div class="ml-auto">
            <a href="admin.php" class="btn btn-outline-light mr-2">Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Manage Sponsored Images</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Upload New Images</h5>
                <form action="" method="POST" enctype="multipart/form-data" class="form-inline">
                    <?= csrf_field() ?>
                    <input type="file" name="images[]" multiple class="form-control mr-2" required>
                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                </form>
                <small class="text-muted">You can select multiple images at once.</small>
            </div>
        </div>

        <table class="table table-bordered bg-white">
            <thead class="thead-light">
                <tr>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($images as $img): ?>
                <tr>
                    <td>
                        <img src="<?= $img['image_path'] ?>" class="img-thumbnail" alt="Sponsored Image">
                    </td>
                    <td>
                        <?php if ($img['is_visible']): ?>
                            <span class="badge badge-success">Visible</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= sign_url('admin_sponsored.php', ['toggle' => $img['id']]) ?>" class="btn btn-sm <?= $img['is_visible'] ? 'btn-warning' : 'btn-success' ?>">
                            <?= $img['is_visible'] ? 'Hide' : 'Show' ?>
                        </a>
                        <a href="<?= sign_url('admin_sponsored.php', ['delete' => $img['id']]) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($images)): ?>
                <tr>
                    <td colspan="3" class="text-center">No images uploaded yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
      const btn = this.querySelector('button[type="submit"], .btn-primary');
      if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
      }
    });
  });
</script>
</body>
</html>
