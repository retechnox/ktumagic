<?php include 'theme.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | KTU Magic</title>
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
            cursor: pointer;
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
            font-size: clamp(8rem, 25vw, 15rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin: 0;
            letter-spacing: -8px;
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
            margin-bottom: 45px;
            line-height: 1.6;
            max-width: 450px;
            margin-inline: auto;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--primary-blue);
            color: white;
            padding: 18px 36px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .back-btn:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.35);
        }

        .back-btn svg {
            transition: transform 0.3s ease;
        }

        .back-btn:hover svg {
            transform: translateX(-4px);
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

        @media (max-width: 480px) {
            .error-code { font-size: 6rem; letter-spacing: -4px; }
            .error-title { font-size: 24px; }
            .error-message { font-size: 16px; }
            .back-btn { width: 100%; justify-content: center; box-sizing: border-box; }
        }
    </style>
</head>
<body onclick="window.location.href='/'">
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    
    <div class="error-container" onclick="event.stopPropagation()">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Magic Not Found</h2>
        <p class="error-message">
            We couldn't find the page you're looking for. It might have vanished into thin air or moved to a new home.
        </p>
        
        <button class="back-btn" onclick="window.location.href='/'">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Homepage
        </button>
    </div>

    <script>
        // Tap anywhere to go home
        document.body.addEventListener('click', function() {
            window.location.href = '/';
        });
    </script>
</body>
</html>
