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
  display: flex !important;
  flex-direction: column !important;
  min-height: 100vh !important;
}
main {
  flex: 1 0 auto;
}

/* ---- IMPROVED THEME TOGGLE (Switch Style) ---- */
.theme-toggle {
  position: relative;
  width: 58px;
  height: 30px;
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
  top: 3px;
  left: 3px;
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
