<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — KTU Magic</title>
    <meta name="description" content="Learn more about KTU Magic, the all-in-one academic support platform for KTU students, trusted by over 40k users.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .about-hero {
            padding: 100px 0 60px;
            text-align: center;
            background: linear-gradient(180deg, rgba(37,99,235,0.05) 0%, transparent 100%);
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .about-hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 800;
            letter-spacing: -2px;
            margin-bottom: 20px;
        }
        .gradient-text {
            background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .about-content {
            padding-bottom: 100px;
        }
        .content-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 60px;
            box-shadow: var(--card-shadow);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 60px;
            text-align: center;
        }
        .stat-item h2 {
            font-family: 'Sora', sans-serif;
            font-size: 42px;
            margin: 0;
            color: var(--primary-blue);
        }
        .stat-item p {
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 5px;
        }
        .mission-text {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-family: 'Inter';
            font-weight: 500;
        }
        .primary-text {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.8;
        }
        @media (max-width: 768px) {
            .content-card { padding: 30px; }
            .about-hero { padding: 80px 0 40px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="about-hero">
        <div class="container fade-el">
            <h1>Our <span class="gradient-text">Mission</span></h1>
            <p class="mission-text" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                Empowering KTU students with the smarter way to study.
            </p>
        </div>
    </section>

    <div class="container about-content">
        <div class="content-card fade-el">
            <h2 style="font-family:'Sora'; margin-top:0; font-size:32px; margin-bottom:25px;">Helping students organize their academic journey.</h2>
            
            <p class="primary-text">
                KTU Magic is an all-in-one academic support platform created to help KTU students make their academic journey easier, smarter, and more organized. 
            </p>
            
            <p class="primary-text">
                Our platform provides easy access to essential study resources such as notes, previous question papers, syllabus, textbooks, important topics, and question banks, along with the latest KTU updates, internship opportunities, and tuition support. 
            </p>

            <p class="primary-text">
                With a growing community of <strong>40k+ active users</strong> already trusting our services, KTU Magic continues to support students by bringing everything they need for their studies together in one convenient place.
            </p>

            <div class="stats-grid">
                <div class="stat-item">
                    <h2>40k+</h2>
                    <p>Active Users</p>
                </div>
                <div class="stat-item">
                    <h2>500+</h2>
                    <p>Study Materials</p>
                </div>
                <div class="stat-item">
                    <h2>24/7</h2>
                    <p>Access</p>
                </div>
                <div class="stat-item">
                    <h2>100%</h2>
                    <p>Free to Use</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
