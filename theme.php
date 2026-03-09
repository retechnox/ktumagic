<?php
// theme.php — Global dark/light mode system
// Include this ONCE in every page's <head> section, BEFORE other stylesheets.
?>
<script>
// Apply theme ASAP to prevent flash
(function(){
  const saved = localStorage.getItem('ktu-theme');
  if(saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)){
    document.documentElement.classList.add('dark');
  }
})();
</script>
<style>
/* ========== LIGHT MODE (DEFAULT) ========== */
:root {
  --bg-primary: #f8fafc;
  --bg-secondary: #ffffff;
  --bg-card: #ffffff;
  --bg-nav: rgba(255, 255, 255, 0.65);
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
  --footer-bg: #0f172a;
  --footer-text: #94a3b8;
  --footer-heading: #ffffff;
  --footer-border: rgba(255,255,255,0.05);
  --input-bg: #ffffff;
  --input-border: #e2e8f0;
}

/* ========== DARK MODE ========== */
:root.dark {
  --bg-primary: #0f172a;
  --bg-secondary: #1e293b;
  --bg-card: #1e293b;
  --bg-nav: rgba(15, 23, 42, 0.85);
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

/* ========== GLOBAL DARK OVERRIDES ========== */
body {
  background: var(--bg-primary) !important;
  color: var(--text-primary) !important;
  transition: background 0.3s ease, color 0.3s ease;
}

/* ---- THEME TOGGLE ---- */
.theme-toggle {
  background: var(--badge-bg);
  border: 1px solid var(--border-color);
  border-radius: 50px;
  padding: 6px 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 16px;
  transition: 0.3s;
  color: var(--text-primary);
  line-height: 1;
}
.theme-toggle:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.theme-toggle .theme-icon-light,
.theme-toggle .theme-icon-dark { display: none; }
:root:not(.dark) .theme-toggle .theme-icon-light { display: inline; }
:root.dark .theme-toggle .theme-icon-dark { display: inline; }
</style>
