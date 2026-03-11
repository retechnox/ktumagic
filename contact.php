<?php
include 'db.php';
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — KTU Magic</title>
    <meta name="description" content="Get in touch with the KTU Magic team for support, inquiries, or feedback.">
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
                    <a href="mailto:support@ktumagic.com" class="contact-link">support@ktumagic.com</a>
                </div>
            </div>

            <div class="contact-card fade-el" style="animation-delay: 100ms;">
                <div class="icon-box" style="background: rgba(37, 211, 102, 0.1); color: #25D366;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467"/></svg>
                </div>
                <div class="contact-info">
                    <h3>WHATSAPP SUPPORT</h3>
                    <p>Join our group for instant updates and study peer support.</p>
                    <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" class="contact-link">Join Group →</a>
                </div>
            </div>

            <div class="contact-card fade-el" style="animation-delay: 200ms;">
                <div class="icon-box" style="background: rgba(37, 99, 235, 0.1);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <div class="contact-info">
                    <h3>Phone Support</h3>
                    <p>Call or Message us for urgent assistance.</p>
                    <a href="tel:+917907552296" class="contact-link">+91 79075 52296</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
