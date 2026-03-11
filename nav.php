<?php include 'theme.php'; ?>
<style>
  :root {
    --neon-purple: #8b5cf6;
    --neon-pink: #ec4899;
    --primary-blue: #2563EB;
  }

  /* ======================= NAVBAR STYLE ======================= */
  nav {
    background: var(--bg-nav);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 15px 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    border-bottom: 1px solid var(--border-color);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
    transition: background 0.3s ease;
  }

  .nav-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: min(1300px, 95vw);
    margin: auto;
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
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 9998;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .mobile-sidebar {
    position: fixed;
    top: 0;
    right: -320px;
    width: 320px;
    height: 100vh;
    background: var(--sidebar-bg);
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
</style>

<nav>
  <div class="container nav-inner">
    <a href="index.php" class="logo">KTU Magic</a>

    <!-- DESKTOP LINKS -->
    <div class="nav-links">
      <a href="/">Home</a>

      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Syllabus <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="#">KTU Syllabus</a>
          <a href="#">Academic Calendar</a>
        </div>
      </div>

      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Notes <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="view_branch.php?scheme_id=2">2024 Scheme</a>
          <a href="view_branch.php?scheme_id=1">2019 Scheme</a>
        </div>
      </div>

      <a href="pyq.php" style="color:var(--primary-blue);">PYQ Search</a>



      <div class="desktop-menu-group">
        <a href="#" class="desktop-dropdown">
          Question Papers <svg class="desktop-dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </a>
        <div class="desktop-submenu">
          <a href="pyq.php?scheme_id=2">2024 Scheme</a>
          <a href="pyq.php?scheme_id=1">2019 Scheme</a>
        </div>
      </div>

      <a href="#">Internships</a>
      <a href="view_scheme.php">KTU Tuitions</a>
      <a href="#">Upload Notes</a>

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
      <a href="search.php" class="p-2 text-gray-500 hover:text-blue-600 transition" aria-label="Search">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px; height:24px;">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </a>

      <!-- NOTIFICATION BELL -->
      <button onclick="window.requestPushPermission()" class="p-2 text-gray-500 hover:text-blue-600 transition" 
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
    <h2>KTUNOTES</h2>
    <button class="close-sidebar" onclick="closeSidebar()">&times;</button>
  </div>

  <div class="sidebar-content">
    <div class="menu-item" onclick="window.requestPushPermission()" style="background: rgba(37, 99, 235, 0.05); color: var(--primary-blue); border-left: 4px solid var(--primary-blue); margin-bottom: 8px;">
        <span>🔔 Enable Live Updates</span>
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </div>

    <a href="index.php" class="menu-item">Home</a>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Syllabus</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="#" class="submenu-item">KTU Syllabus</a>
        <a href="#" class="submenu-item">Academic Calendar</a>
      </div>
    </div>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Notes</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="view_branch.php?scheme_id=2" class="submenu-item">2024 Scheme</a>
        <a href="view_branch.php?scheme_id=1" class="submenu-item">2019 Scheme</a>
      </div>
    </div>

    <a href="pyq.php" class="menu-item" style="color:var(--primary-blue);"><span>PYQ Search</span></a>

    <div class="menu-group">
      <div class="menu-item" onclick="toggleSubmenu(this)">
        <span>Question Papers</span>
        <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="submenu">
        <a href="pyq.php?scheme_id=2" class="submenu-item">2024 Scheme</a>
        <a href="pyq.php?scheme_id=1" class="submenu-item">2019 Scheme</a>
      </div>
    </div>

    <a href="#" class="menu-item"><span>Internships</span></a>
    <a href="view_scheme.php" class="menu-item"><span>KTU Tuitions</span></a>
    <a href="#" class="menu-item"><span>Upload Notes</span></a>

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
        <a href="https://chat.whatsapp.com/LP2seQqrDoC5NX1OErAbSO?mode=gi_t" aria-label="WhatsApp">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
          </svg>
        </a>
        <a href="https://www.instagram.com/ktumagic" aria-label="Instagram"><svg width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
          </svg></a>
        <a href="#" aria-label="Telegram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
      &copy; Copyright 2025 KTU Magic. All rights reserved powered by <a href="#"
        style="color:var(--primary-blue); text-decoration:none;">ktunotes.in</a>
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
</script>
<div style="height: 60px;"></div> <!-- Spacer for fixed nav -->
<main class="flex-grow">