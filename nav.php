<?php
include_once 'db.php';
$jsonData = @file_get_contents(__DIR__ . '/data/data.json') ?: '{}';
$data = json_decode($jsonData, true);
$contact = $data['contact'] ?? [];
?>
<script>
// Apply theme ASAP to prevent flash
(function(){
  const saved = localStorage.getItem('ktu-theme');
  if(saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)){
    document.documentElement.classList.add('dark');
  }
  // Set favicon dynamically
  const link = document.querySelector("link[rel*='icon']") || document.createElement('link');
  link.type = 'image/webp';
  link.rel = 'shortcut icon';
  link.href = 'assets/logooo.webp';
  document.getElementsByTagName('head')[0].appendChild(link);
})();
</script>
<style>
  :root {
    --neon-purple: #8b5cf6;
    --neon-pink: #ec4899;
    --primary-blue: #2563EB;
    
    /* Global Theme Tokens */
    --bg-primary: #f8fafc;
    --bg-secondary: #ffffff;
    --bg-card: #ffffff;
    --bg-nav: #ffffff;
    --bg-dropdown: #ffffff;
    --bg-submenu: #f8fafc;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #64748b;
    --text-subtle: #94a3b8;
    --border-color: rgba(0, 0, 0, 0.05);
    --border-light: #f1f5f9;
    --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --badge-bg: #f1f5f9;
    --badge-color: #475569;
    --sidebar-bg: #ffffff;
    --modal-bg: rgba(15, 23, 42, 0.55);
    --modal-card-bg: #ffffff;
    --hamburger-color: #0f172a;
    --img-border: white;
    --footer-bg: #ffffff;
    --footer-text: #64748b;
    --footer-heading: #0f172a;
    --footer-border: #f1f5f9;
    --input-bg: #ffffff;
    --input-border: #e2e8f0;
  }

  :root.dark {
    --bg-primary: #0f172a;
    --bg-secondary: #1e293b;
    --bg-card: #1e293b;
    --bg-nav: #0f172a;
    --bg-dropdown: #1e293b;
    --bg-submenu: #0f172a;
    --text-primary: #f1f5f9;
    --text-secondary: #cbd5e1;
    --text-muted: #94a3b8;
    --text-subtle: #64748b;
    --border-color: rgba(255, 255, 255, 0.08);
    --border-light: rgba(255, 255, 255, 0.06);
    --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
    --badge-bg: rgba(255,255,255,0.08);
    --badge-color: #cbd5e1;
    --sidebar-bg: #1e293b;
    --modal-bg: rgba(0, 0, 0, 0.7);
    --modal-card-bg: #1e293b;
    --hamburger-color: #f1f5f9;
    --img-border: #334155;
    --footer-bg: #020617;
    --footer-text: #64748b;
    --footer-heading: #e2e8f0;
    --footer-border: rgba(255,255,255,0.04);
    --input-bg: #334155;
    --input-border: #475569;
  }


  /* ======================= NAVBAR STYLE ======================= */
  nav {
    background: var(--bg-nav) !important;
    padding: 15px 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    border-bottom: 1px solid var(--border-color);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
  }

  .nav-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: min(1500px, 95vw);
    margin: auto;
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }
  
  .logo img {
    height: 40px;
    width: auto;
    object-fit: contain;
  }

  .nav-links {
    display: flex;
    align-items: center;
    gap: 18px;
  }

  .nav-links a {
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 700;
    font-size: 13.5px;
    white-space: nowrap;
    transition: color 0.2s;
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

  /* ---------- DESKTOP DROPDOWNS ---------- */
  .desktop-menu-group {
    position: relative;
    display: inline-block;
  }

  .desktop-dropdown {
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 700;
    font-size: 13.5px;
    color: var(--text-primary);
    white-space: nowrap;
    transition: color 0.2s;
  }

  .desktop-dropdown:hover {
    color: var(--primary-blue);
  }

  .desktop-dropdown-icon {
    width: 16px;
    height: 16px;
    transition: transform 0.3s;
  }

  .desktop-menu-group:hover .desktop-dropdown-icon {
    transform: rotate(180deg);
  }

  .desktop-submenu {
    position: absolute;
    top: 100%;
    left: -10px;
    min-width: 200px;
    background: var(--bg-dropdown);
    border-radius: 12px;
    box-shadow: var(--card-shadow);
    padding: 10px 0;
    opacity: 0;
    visibility: hidden;
    margin-top: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    border: 1px solid var(--border-light);
  }

  .desktop-menu-group:hover .desktop-submenu {
    opacity: 1;
    visibility: visible;
    margin-top: 5px;
  }

  .desktop-submenu a {
    display: block;
    padding: 10px 20px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.2s, color 0.2s;
  }

  .desktop-submenu a:hover {
    background: var(--bg-submenu);
    color: var(--primary-blue);
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

  /* ---------- NAV ACTIONS GROUP ---------- */
  .nav-right-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  @media (max-width: 768px) {
    .nav-right-actions {
      gap: 10px;
    }
  }

  /* ---------- MOBILE SIDEBAR ---------- */
  .mobile-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, 0.79);
    backdrop-filter: blur(4px);
    z-index: 9998;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .dark .mobile-overlay {
    background: rgba(15, 23, 42, 0.85);
  }

  .mobile-sidebar {
    position: fixed;
    top: 0;
    right: -320px;
    width: 320px;
    height: 100vh;
    background: var(--sidebar-bg) !important;
    z-index: 9999;
    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
  }

  .mobile-sidebar.open {
    right: 0;
  }

  .sidebar-header {
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-light);
  }

  .sidebar-header h2 {
    font-family: 'Sora', sans-serif;
    font-weight: 800;
    font-size: 24px;
    margin: 0;
    letter-spacing: -1px;
    color: var(--text-primary);
  }

  .close-sidebar {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-muted);
  }

  .sidebar-content {
    padding: 16px 0;
    flex: 1;
  }

  .menu-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 24px;
    font-weight: 700;
    text-decoration: none;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-light);
    cursor: pointer;
    transition: all 0.2s;
    gap: 12px;
  }

  .menu-item span {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .menu-item:hover {
    background: var(--bg-submenu);
  }

  .dropdown-icon {
    width: 18px;
    height: 18px;
    transition: transform 0.3s;
  }

  .menu-item.active .dropdown-icon {
    transform: rotate(180deg);
  }

  .submenu {
    background: var(--bg-submenu);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
  }

  .submenu.open {
    max-height: 500px;
  }

  .submenu-item {
    display: block;
    padding: 12px 40px;
    font-weight: 600;
    text-decoration: none;
    color: var(--text-secondary);
    font-size: 14px;
    transition: color 0.2s;
  }

  .submenu-item:hover {
    color: var(--primary-blue);
  }

  .sidebar-social-box {
    margin: 24px;
    padding: 24px;
    background: var(--bg-submenu);
    border-radius: 12px;
    text-align: center;
  }

  .sidebar-social-box h3 {
    font-size: 16px;
    margin: 0 0 16px;
    color: var(--text-primary);
    font-family: 'Sora', sans-serif;
  }

  .social-grid-sidebar {
    display: flex;
    justify-content: center;
    gap: 20px;
  }

  .social-grid-sidebar a {
    color: var(--text-muted);
    transition: 0.3s;
  }

  .social-grid-sidebar a:hover {
    color: var(--primary-blue);
    transform: translateY(-3px);
  }

  .sidebar-footer {
    padding: 24px;
    border-top: 1px solid var(--border-light);
    background: var(--sidebar-bg);
  }

  .social-links {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    justify-content: center;
  }

  .social-links a {
    color: #0f172a;
    transition: color 0.2s;
  }

  .social-links a:hover {
    color: var(--primary-blue);
  }

  .copyright {
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
    line-height: 1.6;
  }

  @media (max-width: 768px) {
    .nav-links {
      display: none;
    }

    .hamburger {
      display: flex;
    }
  }

  /* ======================= SPOTLIGHT SEARCH ======================= */
  .spotlight-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 2000;
    display: none;
    align-items: flex-start;
    justify-content: center;
    padding-top: 15vh;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .spotlight-overlay.active {
    display: flex;
    opacity: 1;
  }

  .spotlight-card {
    width: min(650px, 90vw);
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.25);
    transform: scale(0.95) translateY(-20px);
    transition: all 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
    overflow: hidden;
  }

  .spotlight-overlay.active .spotlight-card {
    transform: scale(1) translateY(0);
  }

  .spotlight-search-inner {
    display: flex;
    align-items: center;
    padding: 20px 24px;
    gap: 16px;
  }

  .spotlight-input {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    color: var(--text-primary);
    font-size: 20px;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
  }

  .spotlight-input::placeholder {
    color: var(--text-subtle);
  }

  .spotlight-hint {
    padding: 12px 24px;
    background: var(--bg-primary);
    border-top: 1px solid var(--border-color);
    font-size: 13px;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    justify-content: space-between;
  }

  .spotlight-hint kbd {
    background: var(--border-light);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: sans-serif;
    font-size: 11px;
  }

  /* ---- IMPROVED THEME TOGGLE (Switch Style) ---- */
  .theme-toggle {
    position: relative;
    width: 60px;
    height: 32px;
    background: var(--bg-secondary);
    border: 1.5px solid var(--border-color);
    border-radius: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 6px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
  }

  .theme-toggle:hover {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
  }

  .theme-thumb {
    position: absolute;
    top: 3.5px;
    left: 4px;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .theme-toggle svg {
    width: 14px;
    height: 14px;
    transition: 0.3s;
    z-index: 1;
  }

  .sun-icon { color: #f59e0b; opacity: 1; }
  .moon-icon { color: #8b5cf6; opacity: 0.3; }

  :root.dark .theme-thumb {
    transform: translateX(28px);
    background: #1e293b;
  }

  :root.dark .sun-icon { opacity: 0.3; }
  :root.dark .moon-icon { opacity: 1; }

  :root.dark .theme-toggle {
    background: #0f172a;
  }
</style>

<nav>
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <img src="assets/logooo.webp" alt="KTU Magic">
    </a>

    <!-- DESKTOP LINKS -->
    <div class="nav-links">
      <a href="/">Home</a>

      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Notes <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="<?= sign_url('view_scheme.php', ['mode' => 'notes']) ?>">KTU Notes</a>
          <a href="<?= sign_url('view_branch.php', ['scheme_id' => 2]) ?>">2024 Scheme</a>
          <a href="<?= sign_url('view_branch.php', ['scheme_id' => 1]) ?>">2019 Scheme</a>
        </div>
      </div>

      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Syllabus <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="<?= sign_url('syllabus.php', ['scheme_id' => 2]) ?>">2024 Scheme</a>
          <a href="<?= sign_url('syllabus.php', ['scheme_id' => 1]) ?>">2019 Scheme</a>
        </div>
      </div>



      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Question Papers <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="<?= sign_url('pyq.php', ['scheme_id' => 2]) ?>">2024 Scheme</a>
          <a href="<?= sign_url('pyq.php', ['scheme_id' => 1]) ?>">2019 Scheme</a>
        </div>
      </div>

      <a href="internships.php">Internships</a>
      <a href="404.php">KTU Tuitions</a>
      <a href="404.php">Upload Notes</a>

      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          More <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="about.php">About Us</a>
          <a href="contact.php">Contact Us</a>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE ACTIONS -->
    <div class="nav-right-actions">
      <!-- SEARCH ICON -->
      <a href="javascript:void(0)" onclick="openSpotlight()" class="p-2 text-gray-500 hover:text-blue-600 transition" aria-label="Search">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px; height:24px;">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </a>

      <!-- NOTIFICATION BELL -->
      <button id="navNotificationBell" onclick="window.requestPushPermission()" class="p-2 text-gray-500 hover:text-blue-600 transition" 
              aria-label="Enable Notifications" style="background:none; border:none; cursor:pointer; padding:8px; display:flex; align-items:center; position:relative;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px; height:24px;">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
          </path>
        </svg>
      </button>

      <!-- THEME TOGGLE (Switch Style) -->
      <div class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode">
        <div class="theme-thumb">
          <svg class="sun-icon" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z">
            </path>
          </svg>
        </div>
        <svg class="sun-icon" style="opacity: 0.1;" viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z">
          </path>
        </svg>
        <svg class="moon-icon" viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-3.03 0-5.5-2.47-5.5-5.5 0-1.82.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z">
          </path>
        </svg>
      </div>

      <!-- HAMBURGER (MOBILE) -->
      <button class="hamburger" onclick="openSidebar()" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</nav>

<!-- MOBILE SIDEBAR OVERLAY -->
<div id="mobileOverlay" class="mobile-overlay" onclick="closeSidebar()"></div>

<!-- MOBILE SIDEBAR -->
<div id="mobileSidebar" class="mobile-sidebar">
  <div class="sidebar-header">
    <a href="index.php" class="logo">
      <img src="assets/logooo.webp" alt="KTU Magic" style="height: 35px;">
    </a>
    <button class="close-sidebar" onclick="closeSidebar()">&times;</button>
  </div>

  <div class="sidebar-content">
    <div id="sidebarNotificationItem" class="menu-item" onclick="window.requestPushPermission()" style="background: rgba(37, 99, 235, 0.05); color: var(--primary-blue); border-left: 4px solid var(--primary-blue); margin-bottom: 8px;">
        <span>🔔 Enable Live Updates</span>
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </div>

    <a href="index.php" class="menu-item">Home</a>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Notes</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="<?= sign_url('view_scheme.php', ['mode' => 'notes']) ?>" class="submenu-item">KTU Notes</a>
        <a href="<?= sign_url('view_branch.php', ['scheme_id' => 2]) ?>" class="submenu-item">2024 Scheme</a>
        <a href="<?= sign_url('view_branch.php', ['scheme_id' => 1]) ?>" class="submenu-item">2019 Scheme</a>
      </div>
    </div>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Syllabus</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="<?= sign_url('syllabus.php', ['scheme_id' => 2]) ?>" class="submenu-item">2024 Scheme</a>
        <a href="<?= sign_url('syllabus.php', ['scheme_id' => 1]) ?>" class="submenu-item">2019 Scheme</a>
      </div>
    </div>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Question Papers</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="<?= sign_url('pyq.php', ['scheme_id' => 2]) ?>" class="submenu-item">2024 Scheme</a>
        <a href="<?= sign_url('pyq.php', ['scheme_id' => 1]) ?>" class="submenu-item">2019 Scheme</a>
      </div>
    </div>

    <a href="internships.php" class="menu-item"><span>Internships</span></a>
    <a href="404.php" class="menu-item"><span>KTU Tuitions</span></a>
    <a href="404.php" class="menu-item"><span>Upload Notes</span></a>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>More</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="about.php" class="submenu-item">About Us</a>
        <a href="contact.php" class="submenu-item">Contact Us</a>
      </div>
    </div>

    <div class="sidebar-social-box">
      <h3>Follow Us</h3>
      <div class="social-grid-sidebar">
        <a href="<?= $contact['whatsapp_main'] ?? '#' ?>" aria-label="WhatsApp">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
          </svg>
        </a>
        <a href="<?= $contact['instagram'] ?? 'https://www.instagram.com/ktumagic' ?>" aria-label="Instagram"><svg width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
          </svg></a>
        <a href="<?= $contact['telegram'] ?? '#' ?>" aria-label="Telegram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m22 2-7 20-4-9-9-4Z"></path>
            <path d="M22 2 11 13"></path>
          </svg></a>
        <a href="#" aria-label="LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
            <rect x="2" y="9" width="4" height="12"></rect>
            <circle cx="4" cy="4" r="2"></circle>
          </svg></a>
      </div>
    </div>
  </div>

  <div class="sidebar-footer">
    <div class="copyright">
      &copy; Copyright <?= date('Y') ?> KTU Magic. All rights reserved powered by <a href="#"
        style="color:var(--primary-blue); text-decoration:none;">ktumagin.in</a>
    </div>
  </div>
</div>

<!-- SPOTLIGHT SEARCH OVERLAY -->
<div id="spotlightSearch" class="spotlight-overlay" onclick="closeSpotlight()">
  <div class="spotlight-card" onclick="event.stopPropagation()">
    <div class="spotlight-search-inner">
      <svg width="24" height="24" fill="none" stroke="var(--primary-blue)" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
      <input type="text" id="spotlightInput" class="spotlight-input" placeholder="Course name,Code " 
             onkeydown="if(event.key === 'Enter') performSpotlightSearch()">
    </div>
    <div class="spotlight-hint">
      <span>Search KTU Magic</span>
      <span>Press <kbd>Enter</kbd> to search &bull; <kbd>ESC</kbd> to close</span>
    </div>
  </div>
</div>

<script>
  function openSidebar() {
    document.getElementById("mobileOverlay").style.display = "block";
    document.getElementById("mobileSidebar").classList.add("open");
    setTimeout(() => {
      document.getElementById("mobileOverlay").style.opacity = "1";
    }, 10);
  }

  function closeSidebar() {
    document.getElementById("mobileOverlay").style.opacity = "0";
    document.getElementById("mobileSidebar").classList.remove("open");
    setTimeout(() => {
      document.getElementById("mobileOverlay").style.display = "none";
    }, 300);
  }

  function toggleSubmenu(el) {
    const submenu = el.nextElementSibling;
    const isActive = el.classList.contains("active");

    // Close all other submenus maybe? No, let's keep it simple for now as requested.
    el.classList.toggle("active");
    submenu.classList.toggle("open");
  }

  function toggleTheme() {
    const html = document.documentElement;
    html.classList.toggle('dark');
    localStorage.setItem('ktu-theme', html.classList.contains('dark') ? 'dark' : 'light');
  }

  // --- SPOTLIGHT LOGIC ---
  function openSpotlight() {
    const spotlight = document.getElementById("spotlightSearch");
    const input = document.getElementById("spotlightInput");
    spotlight.style.display = "flex";
    setTimeout(() => {
      spotlight.classList.add("active");
      input.focus();
    }, 10);
    document.body.style.overflow = "hidden"; // Prevent scroll
  }

  function closeSpotlight() {
    const spotlight = document.getElementById("spotlightSearch");
    spotlight.classList.remove("active");
    setTimeout(() => {
      spotlight.style.display = "none";
      document.body.style.overflow = ""; // Restore scroll
    }, 300);
  }

  function performSpotlightSearch() {
    const query = document.getElementById("spotlightInput").value.trim();
    if (query) {
      window.location.href = `search.php?q=${encodeURIComponent(query)}`;
    }
  }

  // Global ESC listener
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById("spotlightSearch").classList.contains("active")) {
      closeSpotlight();
    }
  });
</script>
<div style="height: 60px;"></div> <!-- Spacer for fixed nav -->
<main class="flex-grow">