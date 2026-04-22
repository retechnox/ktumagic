<?php
ob_start();
include 'db.php';

// ---- Rate limiting (brute-force protection) ----
$maxAttempts = 5;
$lockoutMinutes = 15;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

// Check lockout
$locked = false;
if ($_SESSION['login_attempts'] >= $maxAttempts) {
    $elapsed = time() - $_SESSION['last_attempt_time'];
    if ($elapsed < ($lockoutMinutes * 60)) {
        $locked = true;
        $remaining = ceil(($lockoutMinutes * 60 - $elapsed) / 60);
        $error = "Too many failed attempts. Try again in {$remaining} minute(s).";
    } else {
        // Lockout expired, reset
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$locked) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Read credentials from .env
    $adminUser = getenv('ADMIN_USERNAME') ?: 'admin';
    $adminPass = getenv('ADMIN_PASSWORD') ?: '';

    if ($adminPass === '') {
        $error = 'Admin credentials not configured. Contact the administrator.';
    } elseif (hash_equals($adminUser, $username) && hash_equals($adminPass, $password)) {
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_attempts'] = 0; // Reset on success

        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();

        $attemptsLeft = $maxAttempts - $_SESSION['login_attempts'];
        if ($attemptsLeft > 0) {
            $error = "Invalid credentials. {$attemptsLeft} attempt(s) remaining.";
        } else {
            $error = "Too many failed attempts. Try again in {$lockoutMinutes} minutes.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - KTU Magic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #1e293b;
            border-radius: 20px;
            padding: 40px;
            width: min(420px, 90vw);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .login-card h2 {
            font-family: 'Sora', sans-serif;
            color: white;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .login-card p.subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: #334155;
            border: 1px solid #475569;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }
        .form-group input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        .btn-login:disabled {
            background: #475569;
            cursor: not-allowed;
            transform: none;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }
        .back-link:hover { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>🔐 Admin Login</h2>
        <p class="subtitle">Enter your credentials to access the admin panel.</p>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" <?= $locked ? 'disabled' : '' ?>>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" <?= $locked ? 'disabled' : '' ?>>
            </div>
            <button type="submit" class="btn-login" <?= $locked ? 'disabled' : '' ?>>
                <?= $locked ? 'Locked Out' : 'Sign In' ?>
            </button>
        </form>
        <a href="index.php" class="back-link">← Back to KTU Magic</a>
    </div>
</body>
</html>
