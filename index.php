<?php
ob_start();
header("Cache-Control: no-transform");
header("Content-Encoding: none");
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>KTU Magic – All-in-One Academic Support for KTU Students</title>
  <meta name="description"
    content="KTU Magic is an all-in-one academic support platform for KTU students, providing easy access to notes, question papers, syllabus, textbooks, and the latest updates.">
  <meta name="keywords"
    content="KTU, KTU Notes, KTU Question Papers, KTU Syllabus, KTU Magic, KTU Results, KTU Academic Support">
  <meta name="author" content="KTU Magic">

  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap"
    rel="stylesheet">
  <?php include 'theme.php'; ?>

  <style>
    :root {
      --neon-purple: #8b5cf6;
      --neon-pink: #ec4899;
      --primary-blue: #2563EB;
      --soft-bg: #f8fafc;
      --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    * {
      box-sizing: border-box;
      -webkit-font-smoothing: antialiased;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      margin: 0;
      padding: 0;
      line-height: 1.5;
      overflow-x: hidden;

    }

    .container {
      width: 93%;
      max-width: 1100px;
      margin: auto;
    }

    /* ======================= NAVBAR (Added Upload CTA) ======================= */
    nav {
      background: var(--bg-nav);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 12px 0;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      border-bottom: 1px solid var(--border-color);
    }

    .nav-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-family: 'Sora', sans-serif;
      font-size: 22px;
      font-weight: 800;
      background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-decoration: none;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text-secondary);
      font-weight: 600;
      font-size: 14px;
    }

    .nav-links a:hover {
      color: var(--primary-blue);
    }

    .upload-cta {
      background: var(--primary-blue);
      color: white !important;
      padding: 8px 18px;
      border-radius: 50px;
      transition: 0.3s;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .upload-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
    }

    .alert-bar {
      width: 100%;
      background: #0f172a;
      color: white;
      padding: 0;
      font-size: 13px;
      font-weight: 500;
      display: flex;
      align-items: center;
      overflow: hidden;
      margin: 15px auto 25px;
      border-radius: 12px;
    }

    .alert-static {
      background: #ef4444;
      color: white;
      padding: 6px 12px;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
      z-index: 10;
      position: relative;
    }

    .marquee-container {
      flex: 1;
      overflow: hidden;
      position: relative;
      display: flex;
      align-items: center;
    }

    .marquee {
      white-space: nowrap;
      animation: marquee 20s linear infinite;
      padding-left: 20px;
    }

    .blink-text {
      animation: blink 1s step-end infinite;
      color: #fbbf24;
      font-weight: 700;
    }

    @keyframes marquee {
      from {
        transform: translateX(100%);
      }

      to {
        transform: translateX(-100%);
      }
    }

    @keyframes blink {
      50% {
        opacity: 0;
      }
    }

    /* ======================= BANNER CAROUSEL ======================= */
    .banner-container {
      width: 100%;
      margin: 0 auto 40px;
    }

    .banner-carousel {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 5;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
      background: var(--bg-card);
    }

    .carousel-slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 0.8s ease-in-out;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .carousel-slide.active {
      opacity: 1;
      z-index: 1;
    }

    .carousel-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.4);
      backdrop-filter: blur(8px);
      border: none;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      cursor: pointer;
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #0f172a;
      font-size: 16px;
      transition: 0.3s;
    }

    .carousel-btn:hover {
      background: rgba(255, 255, 255, 0.7);
      transform: translateY(-50%) scale(1.1);
    }

    .carousel-prev {
      left: 20px;
    }

    .carousel-next {
      right: 20px;
    }

    .carousel-indicators {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      z-index: 10;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.4);
      cursor: pointer;
      transition: all 0.3s;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dot.active {
      background: white;
      transform: scale(1.2);
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    @media (max-width: 600px) {
      .carousel-btn {
        width: 28px;
        height: 28px;
        font-size: 14px;
      }

      .carousel-indicators {
        bottom: 12px;
        gap: 8px;
      }

      .dot {
        width: 8px;
        height: 8px;
      }
    }

    /* ======================= HERO ======================= */
    .hero {
      text-align: center;
      padding: 80px 0 40px;
    }

    .hero h1 {
      font-family: 'Sora', sans-serif;
      font-size: clamp(2.5rem, 8vw, 4rem);
      font-weight: 800;
      letter-spacing: -2px;
      line-height: 1;
      margin-bottom: 15px;
    }

    .hero h1 span {
      background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero p {
      max-width: 600px;
      margin: 0 auto 30px;
      color: var(--text-muted);
      font-size: 1.1rem;
    }

    .badge-main {
      display: inline-block;
      padding: 6px 16px;
      background: rgba(139, 92, 246, 0.1);
      border: 1px solid rgba(139, 92, 246, 0.2);
      border-radius: 50px;
      font-size: 12px;
      font-weight: 700;
      color: var(--neon-purple);
      margin-bottom: 15px;
      text-transform: uppercase;
    }

    .hero-cta-btn {
      display: inline-block;
      background: var(--primary-blue);
      color: white;
      padding: 10px 24px;
      border-radius: 50px;
      font-weight: 700;
      text-decoration: none;
      font-size: 14px;
      box-shadow: 0 10px 15px rgba(37, 99, 235, 0.2);
      transition: 0.3s;
    }

    /* ======================= SLIDER (Contain Fixed) ======================= */
    .slider-container {
      padding: 20px 0;
    }

    .slider {
      position: relative;
      width: 100%;
      max-width: 100%;
      aspect-ratio: 16 / 9;
      max-height: 650px;
      overflow: hidden;
      border-radius: 24px;
      background: var(--bg-card);
      box-shadow: var(--card-shadow);
    }

    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 1s ease-in-out;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .slide.active {
      opacity: 1;
    }

    .slide img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    /* ======================= ICON GRID ======================= */
    .icon-grid {
      width: 100%;
      max-width: 1100px;
      margin: 40px auto;
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(4, 1fr);
      justify-content: center;
      padding-bottom: 10px;
    }

    /* Ensure the black icon containers fill their new space */
    .icon-grid div {
      width: 100%;
      aspect-ratio: 1 / 1;
      /* Keeps them perfectly square as they grow */
    }

    .icon-grid::-webkit-scrollbar {
      height: 6px;
    }


    .icon-grid img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 20px;
      transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 4px solid var(--img-border);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .icon-grid img:hover {
      transform: scale(1.08) rotate(2deg);
    }

    /* ======================= MAIN LAYOUT ======================= */
    .main-flex {
      margin-top: 10px;
      display: flex;
      gap: 40px;
    }

    .left {
      flex: 1;
    }

    .right {
      width: 280px;
      position: sticky;
      top: 100px;
      height: max-content;
    }

    /* ======================= CARD UI ======================= */
    .card {
      background: var(--bg-card);
      border-radius: 5px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
      transition: 0.3s ease;
      text-decoration: none;
      border: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: var(--card-hover-shadow);
    }

    .card img {
      width: 100%;
      object-fit: cover;
    }

    .card-body {
      padding: 20px;
      flex-grow: 1;
    }

    .card-body h3 {
      margin: 0 0 10px;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .badge {
      display: inline-block;
      font-size: 11px;
      font-weight: 700;
      background: var(--badge-bg);
      color: var(--badge-color);
      padding: 4px 10px;
      border-radius: 6px;
      margin: 0 4px 4px 0;
    }

    /* ======================= GRID REFINEMENT ======================= */
    .course-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 24px;
    }

    .scheme-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 20px;
      margin-bottom: 50px;
    }

    /* ======================= ANIMATIONS ======================= */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-el {
      opacity: 0;
      animation: fadeIn 0.6s ease forwards;
    }

    /* ======================= RESPONSIVE ======================= */
    @media (max-width: 1024px) {
      .main-flex {
        flex-direction: column;
      }

      .right {
        width: 100%;
        position: static;
      }

      .hero h1 {
        font-size: 3rem;
      }
    }

    @media (max-width: 600px) {
      .icon-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        width: 95vw;
      }

      .hero {
        padding: 40px 0;
      }
    }


    /* ======================= CTA MODAL ======================= */
    .modal-bg {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      width: 100vw;
      max-width: 100%;
    }

    .modal-box h2 {
      margin: 0 0 8px;
      font-family: 'Sora';
    }

    .modal-box p {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 20px;
    }

    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .8);
      backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .modal-card {
      background: var(--modal-card-bg);
      padding: 24px;
      border-radius: 18px;
      width: min(360px, 90%);
      text-align: center;
      color: var(--text-primary);
    }

    .modal-card h3 {
      margin-bottom: 16px;
      font-family: 'Sora', sans-serif;
    }

    .social-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .social {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      padding: 14px;
      border-radius: 14px;
      color: white;
      font-weight: 700;
      text-decoration: none;
    }

    .social svg {
      width: 28px;
      height: 28px;
    }

    .whatsapp {
      background: #25D366;
    }

    .telegram {
      background: #229ED9;
    }

    .linkedin {
      background: #0A66C2;
    }

    .facebook {
      background: #1877F2;
    }

    .modal-card button {
      margin-top: 14px;
      border: none;
      background: none;
      color: #4373adff;
      font-weight: 800;
      cursor: pointer;
    }

    /* ======================= WHATSAPP COMMUNITY SECTION ======================= */
    .whatsapp-community-section {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 24px;
      padding: 40px;
      text-align: center;
      margin: 60px 0 30px;
      box-shadow: var(--card-shadow);
      position: relative;
      overflow: hidden;
    }

    .whatsapp-community-section::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(37, 211, 102, 0.1) 0%, transparent 70%);
      z-index: 0;
    }

    .whatsapp-logo-wrapper {
      width: 80px;
      height: 80px;
      background: white;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 10px 20px rgba(37, 211, 102, 0.2);
      position: relative;
      z-index: 1;
    }

    .whatsapp-community-section h2 {
      font-family: 'Sora', sans-serif;
      font-size: clamp(20px, 4vw, 28px);
      font-weight: 800;
      margin-bottom: 16px;
      color: var(--text-primary);
      position: relative;
      z-index: 1;
    }

    .whatsapp-community-section p {
      color: var(--text-secondary);
      max-width: 600px;
      margin: 0 auto 24px;
      line-height: 1.6;
      font-size: 15px;
      position: relative;
      z-index: 1;
    }

    .community-stats {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-bottom: 30px;
      flex-wrap: wrap;
      position: relative;
      z-index: 1;
    }

    .stat-badge {
      background: var(--bg-primary);
      padding: 8px 16px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      border: 1px solid var(--border-color);
    }

    .pulse-icon {
      width: 10px;
      height: 10px;
      background: #22c55e;
      border-radius: 50%;
      position: relative;
    }

    .pulse-icon::after {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: 50%;
      border: 2px solid #22c55e;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
        opacity: 1;
      }

      100% {
        transform: scale(2.5);
        opacity: 0;
      }
    }

    .whatsapp-btn-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 400px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .wa-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 24px;
      border-radius: 14px;
      font-weight: 700;
      text-decoration: none;
      transition: 0.3s;
      font-size: 15px;
    }

    .wa-btn-primary {
      background: #25D366;
      color: white;
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
    }

    .wa-btn-primary:hover {
      background: #20ba59;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    }

    .wa-btn-outline {
      background: transparent;
      border: 2px solid #25D366;
      color: #25D366;
    }

    .wa-btn-outline:hover {
      background: rgba(37, 211, 102, 0.05);
      transform: translateY(-2px);
    }

    @media (max-width: 600px) {
      .whatsapp-community-section {
        padding: 30px 20px;
      }
    }

    /* ======================= UNIFIED HERO SECTION ======================= */
    .unified-hero {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 32px;
      margin: 3px auto;
      display: flex;
      align-items: center;
      gap: 60px;
      box-shadow: var(--card-shadow);
      position: relative;
      overflow: hidden;
    }

    .unified-hero::before {
      content: '';
      position: absolute;
      top: -100px;
      left: -100px;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.05) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-text-side {
      flex: 1.2;
      text-align: left;
    }

    .hero-text-side h1 {
      font-family: 'Sora', sans-serif;
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 24px;
      color: var(--text-primary);
    }

    .hero-text-side h1 span {
      background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-text-side p {
      color: var(--text-secondary);
      font-size: 1.05rem;
      line-height: 1.7;
      margin-bottom: 32px;
      max-width: 90%;
    }

    .hero-action-buttons {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .hero-community-side {
      flex: 0.8;
      display: flex;
      flex-direction: column;
      gap: 20px;
      justify-content: center;
      max-width: 450px;
      margin: 0 auto;
    }

    /* Redesigning WhatsApp Card for Unified Side */
    .compact-wa-card {
      background: var(--bg-primary);
      border: 2px solid var(--border-color);
      border-radius: 28px;
      padding: 40px 32px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .compact-wa-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .compact-wa-card.wa-theme {
      border-color: rgba(37, 211, 102, 0.4);
    }

    .compact-wa-card.ig-theme {
      border-color: rgba(225, 48, 108, 0.4);
    }

    .compact-wa-card .whatsapp-logo-wrapper {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .compact-wa-card.wa-theme .whatsapp-logo-wrapper {
      background: rgba(37, 211, 102, 0.1);
    }

    .compact-wa-card.ig-theme .whatsapp-logo-wrapper {
      background: rgba(225, 48, 108, 0.1);
    }

    .compact-wa-card h3 {
      font-family: 'Sora', sans-serif;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 12px;
      text-align: center;
    }

    .compact-wa-card p {
      font-size: 13.5px;
      margin-bottom: 20px;
      line-height: 1.65;
      text-align: center;
      color: var(--text-secondary);
    }

    .compact-wa-card .community-stats {
      margin-bottom: 24px;
      gap: 12px;
      display: flex;
      justify-content: center;
    }

    .compact-wa-card .stat-badge {
      padding: 6px 14px;
      font-size: 12.5px;
      font-weight: 600;
    }

    @media (max-width: 1024px) {
      .unified-hero {
        flex-direction: column;
        padding: 40px;
        text-align: center;
        gap: 48px;
      }

      .hero-text-side {
        text-align: center;
      }

      .hero-text-side p {
        margin-left: auto;
        margin-right: auto;
      }

      .hero-action-buttons {
        justify-content: center;
      }

      .hero-community-side {
        width: 100%;
      }
    }

    @media (max-width: 600px) {
      .unified-hero {
        padding: 32px 20px;
        border-radius: 24px;
      }

      .hero-text-side h1 {
        font-size: 2.2rem;
      }

      .hero-action-buttons {
        flex-direction: column;
        width: 100%;
      }

      .hero-action-buttons a {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
    }


    html,
    body {
      max-width: 100%;
      overflow-x: hidden;
    }


    /* ---------- HAMBURGER ---------- */
    .hamburger {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      gap: 5px;
      flex-direction: column;
    }

    .hamburger span {
      width: 22px;
      height: 2px;
      background: var(--hamburger-color);
      border-radius: 2px;
    }

    /* ---------- MOBILE NAV ---------- */
    .mobile-nav {
      position: fixed;
      top: 100px;
      left: 0;
      width: 100%;
      background: var(--bg-secondary);
      display: none;
      flex-direction: column;
      padding: 20px;
      gap: 14px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
      z-index: 999;
    }

    .mobile-nav a {
      font-weight: 700;
      text-decoration: none;
      color: var(--text-primary);
    }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 768px) {
      .nav-links {
        display: none;
      }

      .hamburger {
        display: flex;
      }
    }
  </style>
</head>

<body>

  <?php include 'nav.php'; ?>

  <div class="container">
    <div class="alert-bar">
      <div class="alert-static">Alert</div>
      <div class="marquee-container">
        <div class="marquee" id="alertMarquee">
          <span class="blink-text">NEW:</span> 2024 Scheme Notes are now available! | KTU Result updates...
        </div>
      </div>
    </div>
  </div>



  <div class="container banner-container">
    <div class="banner-carousel" id="mainCarousel">
      <div class="carousel-slide active">
        <img src="https://placehold.co/1200x400/2563EB/FFF?text=Welcome+to+KTU+Magic+⚡️" alt="Banner 1">
      </div>
      <div class="carousel-slide">
        <img src="https://placehold.co/1200x400/8b5cf6/FFF?text=All+KTU+Notes+in+One+Place" alt="Banner 2">
      </div>
      <div class="carousel-slide">
        <img src="https://placehold.co/1200x400/ec4899/FFF?text=Previous+Year+Question+Papers" alt="Banner 3">
      </div>

      <button class="carousel-btn carousel-prev" onclick="moveSlide(-1)">❮</button>
      <button class="carousel-btn carousel-next" onclick="moveSlide(1)">❯</button>

      <!-- Dots Indicators -->
      <div class="carousel-indicators" id="carouselIndicators"></div>
    </div>
  </div>

  <div class="container">
    <div class="icon-grid">
      <?php
$grid_links = [
  1 => "view_scheme.php", // Syllabus
  2 => "view_scheme.php", // KTU Notes
  3 => "pyq.php", // Question Papers
  4 => "contact.php", // Connect With Us
  5 => "#", // Important Topics
  6 => "#", // Internships
  7 => "view_scheme.php", // KTU Tuitions
  8 => "#" // Text Books
];
for ($i = 1; $i <= 8; $i++): ?>
      <a href="<?= $grid_links[$i]?>" class="fade-el" style="animation-delay: <?= $i * 50?>ms">
        <img src="assets/<?= $i?>.jpg" alt="Icon <?= $i?>">
      </a>
      <?php
endfor; ?>
    </div>
  </div>

  <div class="container main-flex">


    <div class="left">



      <div class="unified-hero fade-el">
        <!-- LEFT: Text & CTA buttons -->
        <div class="hero-text-side">
          <h1> <span>KTU MAGIC</span> .</h1>
          <p>
            KTU Magic is an all-in-one academic support platform created to help KTU students make their academic
            journey easier, smarter, and more organized.
            With a growing community of <strong>50k+ active users</strong>, we bring everything you need — from notes to
            internship updates — into one convenient place.
          </p>
          <div class="hero-action-buttons">
            <a href="view_scheme.php" class="hero-cta-btn" style="padding: 14px 32px; font-size: 15px;">Browse Notes</a>
          </div>
        </div><!-- /.hero-text-side -->

        <!-- RIGHT: Community cards, side-by-side on desktop -->
        <div class="hero-community-side" style="flex-direction: column; gap: 20px;">

          <style>
            @media (min-width: 768px) {
              .community-cards-row {
                display: flex;
                flex-direction: row;
                gap: 16px;
              }
              .community-cards-row .compact-wa-card {
                flex: 1;
                min-width: 0;
              }
            }
            @media (max-width: 767px) {
              .community-cards-row {
                display: flex;
                flex-direction: column;
                gap: 20px;
              }
            }
          </style>

          <div class="community-cards-row">

            <!-- WhatsApp Card -->
            <div class="compact-wa-card wa-theme">
              <div class="whatsapp-logo-wrapper" style="width: 64px; height: 64px; margin-bottom: 16px;">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="#25D366">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
                </svg>
              </div>
              <h3 style="font-family: 'Sora', sans-serif; font-size: 18px; margin-bottom: 12px;">WhatsApp Community</h3>
              <p style="font-size: 13px; margin-bottom: 20px; line-height: 1.5;">Join our Scheme-based WhatsApp group to get KTU live updates, study materials, question papers, notes, model questions, and more.</p>
              <div class="community-stats" style="margin-bottom: 24px; gap: 12px; justify-content: center; display: flex;">
                <div class="stat-badge" style="padding: 6px 12px; font-size: 12px;">
                  <div class="pulse-icon" style="background: #22c55e;"></div> 30k+ live students
                </div>
              </div>
              <div class="whatsapp-btn-group">
                <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" target="_blank"
                  class="wa-btn wa-btn-primary" style="padding: 12px; font-size: 14px;">2024 Scheme</a>
                <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" target="_blank"
                  class="wa-btn wa-btn-outline" style="padding: 10px; font-size: 13px; border-width: 1px;">2019 Scheme</a>
              </div>
            </div>

            <!-- Instagram Card -->
            <div class="compact-wa-card ig-theme">
              <div class="whatsapp-logo-wrapper"
                style="width: 64px; height: 64px; margin-bottom: 16px; box-shadow: 0 10px 20px rgba(225, 48, 108, 0.2);">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="#E1306C">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                </svg>
              </div>
              <h3 style="font-family: 'Sora', sans-serif; font-size: 18px; margin-bottom: 12px;">Instagram Community</h3>
              <p style="font-size: 13px; margin-bottom: 20px; line-height: 1.5;">Join our 65K+ student community on Instagram! 🎓<br>We share KTU live updates, important topics, study materials, trolls, internship opportunities, and more.</p>
              <div class="community-stats" style="margin-bottom: 24px; gap: 12px; justify-content: center; display: flex;">
                <div class="stat-badge" style="padding: 6px 12px; font-size: 12px;">
                  <div class="pulse-icon" style="background: #E1306C;"></div> 65k+ live students
                </div>
              </div>
              <div class="whatsapp-btn-group">
                <a href="https://www.instagram.com/ktumagic" target="_blank" class="wa-btn"
                  style="background: #E1306C; color: white; padding: 12px; font-size: 14px; box-shadow: 0 4px 15px rgba(225, 48, 108, 0.3);">Follow on Instagram</a>
              </div>
            </div>

          </div><!-- /.community-cards-row -->
        </div><!-- /.hero-community-side -->
      </div><!-- /.unified-hero -->




        <?php
// Sponsored Images Section
try {
  $stmt = $pdo->query("SELECT image_path FROM sponsored_images WHERE is_visible = 1 ORDER BY created_at DESC");
  $sponsoredImages = $stmt->fetchAll();
}
catch (PDOException $e) {
  $sponsoredImages = [];
}

if (!empty($sponsoredImages)): ?>
        <style>
          .sponsored-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin: 60px 0;
          }

          .sponsored-grid .sponsored-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: 0.3s ease;
            position: relative;
          }

          .sponsored-grid .sponsored-card:hover {
            transform: translateY(-8px);
          }

          .sponsored-grid .sponsored-card img {
            width: 100%;
            height: auto;
            display: block;
          }

          .sponsored-badge-overlay {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }

          @media (max-width: 992px) {
            .sponsored-grid {
              grid-template-columns: repeat(2, 1fr);
              gap: 20px;
            }
          }

          @media (max-width: 600px) {
            .sponsored-grid {
              grid-template-columns: 1fr;
              gap: 16px;
              margin: 40px 0;
            }
          }
        </style>
        <div class="sponsored-grid fade-el">
          <?php foreach ($sponsoredImages as $img): ?>
          <div class="sponsored-card">
            <span class="sponsored-badge-overlay">Sponsored</span>
            <img src="<?= $img['image_path']?>" alt="Sponsored Content" loading="lazy">
          </div>
          <?php
  endforeach; ?>
        </div>
        <?php
endif; ?>

        <h2 style="font-family:'Sora'; font-size:28px; margin:60px 0 25px;">ALL SCHEMES</h2>
        <div class="scheme-grid">
          <a href="view_branch.php?scheme_id=1" class="card scheme-card fade-el">
            <img src="assets/2019/1.jpg" alt="2019">
            <div class="card-body">
              <h3 style="margin:0;">2019 Scheme</h3>
              <p style="color:var(--primary-blue); font-size:13px; margin-top:8px; font-weight:600;">BROWSE BRANCHES →
              </p>
            </div>
          </a>
          <a href="view_branch.php?scheme_id=2" class="card scheme-card fade-el">
            <img src="assets/2025/1.jpg" alt="2024">
            <div class="card-body">
              <h3 style="margin:0;">2024 Scheme</h3>
              <p style="color:var(--primary-blue); font-size:13px; margin-top:8px; font-weight:600;">BROWSE BRANCHES →
              </p>
            </div>
          </a>
        </div>


        <h2 style="font-family:'Sora'; font-size:28px; margin:60px 0 25px;">
          Latest Updates
        </h2>

        <?php
$updates = [];
for ($i = 1; $i <= 3; $i++) {
  if (file_exists(__DIR__ . "/assets/updates/$i.png")) {
    $updates[] = $i;
  }
}
?>

        <?php if (count($updates) > 0): ?>
        <div class="updates-grid">
          <?php foreach ($updates as $i): ?>
          <div class="update-card fade-el">
            <img src="assets/updates/<?= $i?>.png" alt="Update <?= $i?>">
            <div class="update-body">
              <h3>Update
                <?= $i?>
              </h3>
              <p>Latest KTU related announcement.</p>
            </div>
          </div>
          <?php
  endforeach; ?>
        </div>
        <?php
else: ?>
        <p style="color:#64748b; font-size:14px;">
          No recent updates available.
        </p>
        <?php
endif; ?>

      </div>



      <!-- <div class="right">
    <div class="sponsor-card fade-el">
      <h3 style="color:var(--primary-blue); font-size:1.1rem; margin-top:0;">Sponsored</h3>
      <img src="assets/sponsered.jpeg" style="width:100%; border-radius:12px; margin-bottom:10px;">
    </div>
  </div> -->

    </div>

    <!-- SOCIAL MODAL -->
    <div id="socialModal" class="modal-backdrop">
      <div class="modal-card">
        <h3>JOIN US</h3>
        <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 14px; line-height: 1.5;">
          Welcome students and educators! Join our community to access academic resources, stay updated with KTU
          information, and be part of a growing learning network.
        </p>

        <div class="social-grid">
          <a href="https://www.instagram.com/ktumagic" target="_blank" class="social instagram"
            style="background:#E1306C; color:white;">
            <svg viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
            </svg>
            INSTAGRAM
          </a>

          <a href="https://t.me/ktumagic" target="_blank" class="social telegram">
            <svg viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M20.665 3.717l-17.73 6.837c-1.213.486-1.203 1.163-.222 1.462l4.552 1.42 1.566 4.802c.188.518.093.723.475.723.296 0 .43-.135.594-.293l2.394-2.327 4.98 3.68c.918.506 1.577.246 1.807-.85l3.268-15.396c.335-1.343-.513-1.952-1.394-1.56z" />
            </svg>
            TELEGRAM
          </a>

          <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" target="_blank" class="social whatsapp">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
            </svg>
            2019 SCHEME
          </a>

          <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" target="_blank" class="social whatsapp">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
            </svg>
            2024 SCHEME
          </a>
        </div>

        <button onclick="closeSocial()">Close</button>
      </div>
    </div>

    <!-- <div id="updateModal" class="modal-backdrop">
  <div class="modal-card">
    <h3 id="updateTitle"></h3>
    <p id="updateContent"></p>
    <button onclick="closeUpdate()">Close</button>
  </div>
</div> -->


    <script>
      // Banner Carousel Logic
      let currentSlide = 0;
      const slides = document.querySelectorAll(".carousel-slide");
      const indicators = document.getElementById("carouselIndicators");

      // Generate dots
      slides.forEach((_, i) => {
        const dot = document.createElement("div");
        dot.classList.add("dot");
        if (i === 0) dot.classList.add("active");
        dot.onclick = () => {
          clearInterval(slideInterval);
          showSlide(i);
          slideInterval = setInterval(() => moveSlide(1), 5000);
        };
        indicators.appendChild(dot);
      });

      function showSlide(n) {
        slides.forEach(s => s.classList.remove("active"));
        currentSlide = (n + slides.length) % slides.length;
        slides[currentSlide].classList.add("active");

        // Update dots
        document.querySelectorAll(".dot").forEach((d, i) => {
          d.classList.toggle("active", i === currentSlide);
        });
      }

      function moveSlide(n) {
        showSlide(currentSlide + n);
      }

      // Auto-play
      let slideInterval = setInterval(() => moveSlide(1), 5000);

      // Reset interval on manual click
      document.querySelectorAll('.carousel-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          clearInterval(slideInterval);
          slideInterval = setInterval(() => moveSlide(1), 5000);
        });
      });

      const socialModal = document.getElementById("socialModal");

      window.addEventListener("load", () => {
        socialModal.style.display = "flex";
      });

      function closeSocial() {
        socialModal.style.display = "none";
      }

      fetch("update.json")
        .then(res => res.json())
        .then(data => {
          document.getElementById("updateTitle").textContent = data.title;
          document.getElementById("updateContent").textContent = data.content;
          document.getElementById("updateModal").style.display = "flex";
        })
        .catch(() => { });

      function closeUpdate() {
        document.getElementById("updateModal").style.display = "none";
      }

      function toggleMobileNav() {
        const nav = document.getElementById("mobileNav");
        nav.style.display = nav.style.display === "flex" ? "none" : "flex";
      }

      fetch("updates_upperupdate.json", { cache: "no-store" })
        .then(res => res.json())
        .then(data => {
          if (data.message) {
            document.getElementById("alertMarquee").textContent = data.message;
          }
        })
        .catch(() => {
          document.getElementById("alertMarquee").textContent =
            "✨ Stay tuned for the latest KTU updates ✨";
        });
    </script>
    <style>
      .floating-whatsapp {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #25D366;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 4px 15px rgba(37, 211, 102, 0.4);
        z-index: 1000;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .floating-whatsapp:hover {
        transform: scale(1.1) translateY(-2px);
        box-shadow: 0px 6px 20px rgba(37, 211, 102, 0.6);
        color: white;
      }

      @media (max-width: 768px) {
        .floating-whatsapp {
          bottom: 20px;
          right: 20px;
          width: 55px;
          height: 55px;
        }
      }
    </style>

    <a href="https://wa.me/917907552296" target="_blank" class="floating-whatsapp" aria-label="Chat on WhatsApp">
      <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
      </svg>
    </a>

    <?php include 'footer.php'; ?>
</body>

</html>
<?php ob_end_flush(); ?>