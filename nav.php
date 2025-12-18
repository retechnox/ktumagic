<nav>
  <div class="container nav-inner">
    <a href="index.php" class="logo">KTU Magic</a>
    <div class="nav-links">
      <a href="#">Upload Notes</a>
      <a href="#">Courses</a>
      <a href="view_scheme.php" class="upload-cta">Explore Notes</a>
    </div>
  </div>
</nav>

<style>
    .container { width: min(1300px, 95vw); margin: auto; }
    .nav-inner { display: flex; justify-content: space-between; align-items: center; }

.logo {
  font-family: 'Sora', sans-serif;
  font-size: 22px;
  font-weight: 800;
  background: linear-gradient(to right, var(--primary-blue), var(--neon-purple));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-decoration: none;
}

.nav-links { display: flex; align-items: center; gap: 20px; }
.nav-links a { text-decoration: none; color: #475569; font-weight: 600; font-size: 14px; }
.nav-links a:hover { color: var(--primary-blue); }

    </style>