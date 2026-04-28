<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }
$jsonData = file_get_contents(__DIR__ . '/data/data.json');
$data = json_decode($jsonData, true);
$contact = $data['contact'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — KTU Magic</title>
    <meta name="description" content="Get in touch with the KTU Magic team for support, inquiries, or feedback.">
    <meta name="keywords" content="KTU, KTU Notes, KTU Question Papers, KTU Syllabus, KTU Magic, KTU Login, KTU Results, KTU Academic Support, Best KTU Study Materials, Engineering Students Kerala, KTU 2019 Scheme, KTU 2024 Scheme, KTU B.Tech Notes, Engineering Course Materials, KTU Previous Year Questions, KTU PYQ, Semester Exam Notes, KTU Student Portal, KTU Module-wise Notes, KTU Textbook PDF, KTU Notifications, KTU Exam Updates, KTU Civil Engineering, KTU Computer Science, KTU Mechanical Engineering, KTU Electronics Engineering, KTU Electrical Engineering, KTU Semester Results, Engineering Study App, KTU Resource Hub">
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
        .hero-section {
            padding: 100px 0 60px;
            text-align: center;
            background: linear-gradient(180deg, rgba(37,99,235,0.05) 0%, transparent 100%);
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .hero-section h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2.5rem, 8vw, 3.5rem);
            font-weight: 800;
            letter-spacing: -2px;
            margin-bottom: 20px;
        }
        .gradient-text {
            background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding-bottom: 100px;
        }
        .contact-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: var(--card-shadow);
            transition: 0.3s;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }
        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }
        .contact-info h3 {
            font-family: 'Sora', sans-serif;
            margin: 0 0 10px;
            font-size: 20px;
        }
        .contact-info p {
            color: var(--text-muted);
            margin: 0 0 20px;
        }
        .contact-link {
            display: inline-block;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .contact-grid { grid-template-columns: 1fr; }
            .hero-section { padding: 80px 0 40px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="hero-section">
        <div class="container fade-el">
            <h1>Get in <span class="gradient-text">Touch</span></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto 30px;">
                Have a question, feedback, or need academic support? We're here to help! 
            </p>
        </div>
    </section>

    <div class="container">
        <div class="contact-grid">
            <div class="contact-card fade-el">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div class="contact-info">
                    <h3>Email Support</h3>
                    <p>Send us an email and we'll get back to you within 24 hours.</p>
                    <a href="mailto:<?= safe($contact['email'] ?? 'support@ktumagic.in') ?>" class="contact-link"><?= safe($contact['email'] ?? 'support@ktumagic.in') ?></a>
                </div>
            </div>

            <div class="contact-card fade-el" style="animation-delay: 100ms;">
                <div class="icon-box" style="background: rgba(37, 211, 102, 0.1); color: #25D366;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467"/></svg>
                </div>
                <div class="contact-info">
                    <h3>WHATSAPP SUPPORT</h3>
                    <p>Join our groups for instant updates and study peer support.</p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="<?= safe($contact['whatsapp_2024'] ?? 'https://chat.whatsapp.com/KVF5tjauFFEH3eq7HpCYsn?mode=gi_t') ?>" class="contact-link">2024 Scheme Group →</a>
                        <a href="<?= safe($contact['whatsapp_2019'] ?? 'https://chat.whatsapp.com/CD8bPjElkgXAmIukTiIPV9?mode=gi_t') ?>" class="contact-link">2019 Scheme Group →</a>
                    </div>
                </div>
            </div>

            <div class="contact-card fade-el" style="animation-delay: 200ms;">
                <div class="icon-box" style="background: rgba(225, 48, 108, 0.1); color: #E1306C;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </div>
                <div class="contact-info">
                    <h3>Instagram</h3>
                    <p>Follow us for the latest updates and study tips.</p>
                    <a href="https://www.instagram.com/ktumagic" target="_blank" class="contact-link">@ktumagic →</a>
                </div>
            </div>

            <div class="contact-card fade-el" style="animation-delay: 300ms;">
                <div class="icon-box" style="background: rgba(34, 158, 217, 0.1); color: #229ED9;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.665 3.717l-17.73 6.837c-1.213.486-1.203 1.163-.222 1.462l4.552 1.42 1.566 4.802c.188.518.093.723.475.723.296 0 .43-.135.594-.293l2.394-2.327 4.98 3.68c.918.506 1.577.246 1.807-.85l3.268-15.396c.335-1.343-.513-1.952-1.394-1.56z"/></svg>
                </div>
                <div class="contact-info">
                    <h3>Telegram</h3>
                    <p>Join our Telegram channel for fast document downloads.</p>
                    <a href="https://tx.me/ktu_studymaterials" target="_blank" class="contact-link">Join Channel →</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
