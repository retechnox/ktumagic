<?php include 'theme.php'; 
include 'db.php';
$course_id = intval($_GET['course_id'] ?? 0);
$contribute_url = sign_url('submit_material.php', ['course_id' => $course_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources Coming Soon | KTU Magic</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563EB;
        }
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Sora', sans-serif;
            overflow: hidden;
            transition: background 0.3s ease;
        }

        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
            z-index: 10;
        }

        .error-code {
            font-size: clamp(6rem, 20vw, 12rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin: 0;
            letter-spacing: -6px;
            filter: drop-shadow(0 10px 20px rgba(37, 99, 235, 0.1));
        }

        .error-title {
            font-size: 32px;
            font-weight: 800;
            margin: 20px 0 15px;
            letter-spacing: -1px;
        }

        .error-message {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 40px;
            line-height: 1.6;
            max-width: 450px;
            margin-inline: auto;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .contribute-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 18px 36px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25);
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .contribute-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.35);
        }

        .back-btn {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .back-btn:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* Ambient Background Elements */
        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(40px);
            animation: float 10s infinite alternate ease-in-out;
        }

        .glow-1 { top: -10%; left: -10%; background: radial-gradient(circle, rgba(139, 92, 246, 0.05) 0%, transparent 70%); }
        .glow-2 { bottom: -10%; right: -10%; }

        @keyframes float {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(30px, 20px) scale(1.1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Work in Progress!</h2>
        <p class="error-message">
            We are currently gathering resources for this topic. Help us and your fellow students by contributing what you have!
        </p>
        
        <div class="btn-group">
            <a href="<?= safe($contribute_url) ?>" class="contribute-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Contribute Material
            </a>
            <a href="javascript:history.back()" class="back-btn">Go Back</a>
        </div>
    </div>
</body>
</html>
