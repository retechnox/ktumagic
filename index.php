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
<title>KTU Magic – Notes & Resources</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">

<style>
:root {
  --neon-purple: #8b5cf6;
  --neon-pink: #ec4899;
  --primary-blue: #2563EB;
  --soft-bg: #f8fafc;
  --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}

* { box-sizing: border-box; -webkit-font-smoothing: antialiased; }

body {
  font-family: 'Inter', sans-serif;
  background: var(--soft-bg);
  color: #1e293b;
  margin: 0;
  padding: 0;
  line-height: 1.5;
  overflow-x: hidden;
  
}

.container { width: min(1300px, 95vw); margin: auto; }

/* ======================= NAVBAR (Added Upload CTA) ======================= */
nav {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 12px 0;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

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

.upload-cta {
  background: var(--primary-blue);
  color: white !important;
  padding: 8px 18px;
  border-radius: 50px;
  transition: 0.3s;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}
.upload-cta:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3); }

/* ======================= ALERT BAR ======================= */
.alert-bar {
  margin-top: 65px;
  background: #0f172a;
  color: white;
  padding: 10px 0;
  font-size: 13px;
  font-weight: 500;
}

.marquee {
  overflow: hidden;
  white-space: nowrap;
  animation: marquee 25s linear infinite;
}

@keyframes marquee {
  from { transform: translateX(50%); }
  to   { transform: translateX(-100%); }
}

/* ======================= HERO ======================= */
.hero { text-align: center; padding: 80px 0 40px; }
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
.hero p { max-width: 600px; margin: 0 auto 30px; color: #64748b; font-size: 1.1rem; }

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
  display: inline-block; background: var(--primary-blue); color: white;
  padding: 10px 24px; border-radius: 50px; font-weight: 700; text-decoration: none;
  font-size: 14px; box-shadow: 0 10px 15px rgba(37, 99, 235, 0.2); transition: 0.3s;
}

/* ======================= SLIDER (Contain Fixed) ======================= */
.slider-container { padding: 20px 0; }
.slider {
  position: relative;
 width: 100%;
  max-width: 100%;
  aspect-ratio: 16 / 9;
  max-height: 650px;
  overflow: hidden;
  border-radius: 24px;
  background: #ffffff;
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

.slide.active { opacity: 1; }
.slide img { width: 100%; height: 100%; object-fit: contain; }

/* ======================= ICON GRID ======================= */
.icon-grid {
    width: 80vw;             /* Specifically sets the width to 80% of the screen */
    margin: 40px auto;       /* Keeps it centered horizontally */
    display: grid;
    gap: 20px;               /* Space between icons */
    
    /* This makes 4 equal columns that fill the 80vw space */
    grid-template-columns: repeat(4, 1fr); 
    justify-content: center;
    padding-bottom: 10px;
}

/* Ensure the black icon containers fill their new space */
.icon-grid div { 
    width: 100%;
    aspect-ratio: 1 / 1;    /* Keeps them perfectly square as they grow */
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
  border: 4px solid white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.icon-grid img:hover {
  transform: scale(1.08) rotate(2deg);
}

/* ======================= MAIN LAYOUT ======================= */
.main-flex { margin-top: 60px; display: flex; gap: 40px; }
.left { flex: 1; }
.right { width: 280px; position: sticky; top: 100px; height: max-content; }

/* ======================= CARD UI ======================= */
.card {
  background: white;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: var(--card-shadow);
  transition: 0.3s ease;
  text-decoration: none;
  border: 1px solid rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
}
.card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
.card img { width: 100%; object-fit: cover; }
.card-body { padding: 20px; flex-grow: 1; }
.card-body h3 { margin: 0 0 10px; font-size: 1.1rem; font-weight: 700; color: #0f172a; }

.badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  background: #f1f5f9;
  color: #475569;
  padding: 4px 10px;
  border-radius: 6px;
  margin: 0 4px 4px 0;
}

/* ======================= GRID REFINEMENT ======================= */
.course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; }
.scheme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; margin-bottom: 50px; }

/* ======================= ANIMATIONS ======================= */
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.fade-el { opacity: 0; animation: fadeIn 0.6s ease forwards; }

/* ======================= RESPONSIVE ======================= */
@media (max-width: 1024px) {
  .main-flex { flex-direction: column; }
  .right { width: 100%; position: static; }
  .hero h1 { font-size: 3rem; }
}

@media (max-width: 600px) {
.icon-grid { 
    grid-template-columns: repeat(2, 1fr); 
    grid-template-rows: repeat(4, auto);
    gap: 10px;
  }
  .hero { padding: 40px 0; }
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
  background: rgba(15,23,42,.8);
  backdrop-filter: blur(8px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-card {
  background: #fff;
  padding: 24px;
  border-radius: 18px;
  width: min(360px, 90%);
  text-align: center;
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

.whatsapp { background: #25D366; }
.telegram { background: #229ED9; }
.linkedin { background: #0A66C2; }
.facebook { background: #1877F2; }

.modal-card button {
  margin-top: 14px;
  border: none;
  background: none;
  color: #64748b;
  cursor: pointer;
}


html, body {
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
  background: #0f172a;
  border-radius: 2px;
}

/* ---------- MOBILE NAV ---------- */
.mobile-nav {
  position: fixed;
  top: 100px;
  left: 0;
  width: 100%;
  background: white;
  display: none;
  flex-direction: column;
  padding: 20px;
  gap: 14px;
  box-shadow: 0 10px 25px rgba(0,0,0,.1);
  z-index: 999;
}

.mobile-nav a {
  font-weight: 700;
  text-decoration: none;
  color: #0f172a;
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

<nav>
  <div class="container nav-inner">
    <a href="index.php" class="logo">KTU Magic</a>

    <!-- DESKTOP LINKS -->
    <div class="nav-links">
   <a href="#">Syllabus</a>
    <a href="#">KTU Notes</a>
    <a href="view_scheme.php">Question Papers</a>
    <!-- <a href="#">Connect With Us</a> -->
    <a href="#">Important Topics</a>
    <a href="#">Internships</a>
    <a href="view_scheme.php">KTU Tuitions</a>
    <a href="#">Text Books</a>

    <hr>

    <a href="#">Upload Notes</a>
    <a href="view_scheme.php">Courses</a>
    <a href="view_scheme.php" class="upload-cta">Explore Notes</a>
    </div>

    <!-- HAMBURGER (MOBILE) -->
    <button class="hamburger" onclick="toggleMobileNav()" aria-label="Menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>

  <!-- MOBILE NAV -->
  <div id="mobileNav" class="mobile-nav">
    <a href="#">Syllabus</a>
    <a href="#">KTU Notes</a>
    <a href="view_scheme.php">Question Papers</a>
    <!-- <a href="#">Connect With Us</a> -->
    <a href="#">Important Topics</a>
    <a href="#">Internships</a>
    <a href="view_scheme.php">KTU Tuitions</a>
    <a href="#">Text Books</a>
    <hr>

    <a href="#">Upload Notes</a>
    <a href="#">Courses</a>
    <a href="view_scheme.php" class="upload-cta">Explore Notes</a>
  </div>
</nav>



<div class="alert-bar">
  <div class="container">
    <div class="marquee" id="alertMarquee">  Loading updates…</div>
  </div>
</div>

<section class="hero container">
  <div class="badge-main">The Ultimate Resource Hub ⚡️</div>
  <h1 class="fade-el">The KTU <span>Plug.</span></h1>
  <p class="fade-el">Stop hunting through 100 WhatsApp groups. We’ve got your <strong>notes, schemes, and courses</strong> highkey sorted for you.</p>
  <a href="view_scheme.php" class="hero-cta-btn">Start Studying ⚡️</a>
</section>

<div class="container slider-container">
  <div class="slider" id="slider">
    <div class="slide active"><img referrerpolicy="no-referrer" src="assets/slider2.jpg"></div>
    <div class="slide"><img src="assets/slider1.jpg"></div>
  </div>
</div>

<div class="container">
  <div class="icon-grid">
    <?php for($i=1;$i<=8;$i++): ?>
      <img src="assets/<?= $i ?>.jpg" class="fade-el" style="animation-delay: <?= $i * 50 ?>ms">
    <?php endfor; ?>
  </div>
</div>

<div class="container main-flex">


  <div class="left">

    <!-- <h2 id="courses" style="font-family:'Sora'; font-size:28px; margin-bottom:25px;">Latest Courses</h2>
    <div class="course-grid">
    <?php
    $sql = "SELECT c.*, b.name AS branch_name, s.name AS scheme_name FROM courses c LEFT JOIN branches b ON c.branch_id = b.id LEFT JOIN schemes s ON c.scheme_id = s.id ORDER BY c.id DESC LIMIT 12";
    $courses = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $DEFAULT_IMG = "assets/default_course.jpg";

    foreach ($courses as $c):
      $img = !empty($c['image_path']) ? $c['image_path'] : $DEFAULT_IMG;
    ?>
    <a href="view_link.php?course_id=<?= $c['id'] ?>" class="card fade-el">
      <img src="<?= htmlspecialchars($img) ?>" alt="Course" referrerpolicy="no-referrer">
      <div class="card-body">
        <h3><?= htmlspecialchars($c['name']) ?></h3>
        <div style="margin-bottom: 12px;">
            <span class="badge">S<?= htmlspecialchars($c['semester']) ?></span>
            <span class="badge"><?= htmlspecialchars($c['branch_name'] ?? 'Gen Ed') ?></span>
            <span class="badge" style="background:#f3e8ff; color:#7e22ce;"><?= htmlspecialchars($c['scheme_name'] ?? '2019') ?></span>
        </div>
        <p style="color:var(--primary-blue); font-weight:700; font-size:14px;">Open Course →</p>
      </div>
    </a>
    <?php endforeach; ?>
    </div> -->

    <h2 style="font-family:'Sora'; font-size:28px; margin:60px 0 25px;">ALL SCHEMES</h2>
    <div class="scheme-grid">
      <a href="view_branch.php?scheme_id=1" class="card scheme-card fade-el">
        <img src="assets/2019/1.jpg" alt="2019">
        <div class="card-body">
          <h3 style="margin:0;">2019 Scheme</h3>
          <p style="color:var(--primary-blue); font-size:13px; margin-top:8px; font-weight:600;">BROWSE BRANCHES →</p>
        </div>
      </a>
      <a href="view_branch.php?scheme_id=2" class="card scheme-card fade-el">
        <img src="assets/2025/1.jpg" alt="2024">
        <div class="card-body">
          <h3 style="margin:0;">2024 Scheme</h3>
          <p style="color:var(--primary-blue); font-size:13px; margin-top:8px; font-weight:600;">BROWSE BRANCHES →</p>
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
        <img src="assets/updates/<?= $i ?>.png" alt="Update <?= $i ?>">
        <div class="update-body">
          <h3>Update <?= $i ?></h3>
          <p>Latest KTU related announcement.</p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p style="color:#64748b; font-size:14px;">
    No recent updates available.
  </p>
<?php endif; ?>

  </div>

  <div class="right">
    <div class="sponsor-card fade-el">
      <h3 style="color:var(--primary-blue); font-size:1.1rem; margin-top:0;">Sponsored</h3>
      <img src="assets/sponsered.jpeg" style="width:100%; border-radius:12px; margin-bottom:10px;">
    </div>
  </div>
</div>

<!-- SOCIAL MODAL -->
<div id="socialModal" class="modal-backdrop">
  <div class="modal-card">
    <h3>Join Us</h3>

    <div class="social-grid">
      <a href="https://wa.me/XXXXXXXXXX" target="_blank" class="social whatsapp">
        <!-- WhatsApp SVG -->
        <svg viewBox="0 0 32 32"><path fill="currentColor" d="M19.11 17.56c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.13-.42-2.15-1.35-.79-.7-1.33-1.56-1.49-1.83-.16-.27-.02-.41.12-.55.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.02-.22-.53-.45-.46-.61-.47-.16-.01-.34-.01-.52-.01-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27s.97 2.63 1.11 2.81c.14.18 1.9 2.9 4.6 4.07.64.28 1.14.45 1.53.58.64.2 1.23.17 1.69.1.52-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/></svg>
        WhatsApp
      </a>

      <a href="https://t.me/XXXXXXX" target="_blank" class="social telegram">
        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M9.04 15.47l-.38 5.36c.54 0 .78-.23 1.06-.51l2.54-2.43 5.26 3.85c.97.53 1.67.25 1.93-.9l3.5-16.38h0c.31-1.45-.53-2.02-1.48-1.67L1.66 9.41c-1.42.55-1.4 1.33-.25 1.68l5.37 1.68L18.9 6.18c.57-.35 1.1-.16.67.19"/></svg>
        Telegram
      </a>

      <a href="https://linkedin.com/company/XXXX" target="_blank" class="social linkedin">
        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.22 8h4.56v14H.22V8zM8.54 8h4.37v1.91h.06c.61-1.16 2.11-2.39 4.35-2.39 4.65 0 5.51 3.06 5.51 7.04V22h-4.56v-6.62c0-1.58-.03-3.62-2.21-3.62-2.21 0-2.55 1.72-2.55 3.5V22H8.54V8z"/></svg>
        LinkedIn
      </a>

      <a href="https://facebook.com/XXXX" target="_blank" class="social facebook">
        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M22.68 0H1.32C.59 0 0 .59 0 1.32v21.36C0 23.41.59 24 1.32 24h11.5v-9.29H9.69V11.1h3.13V8.41c0-3.1 1.89-4.79 4.65-4.79 1.32 0 2.46.1 2.79.14v3.24h-1.91c-1.5 0-1.79.71-1.79 1.76v2.3h3.58l-.47 3.61h-3.11V24h6.09c.73 0 1.32-.59 1.32-1.32V1.32C24 .59 23.41 0 22.68 0z"/></svg>
        Facebook
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
// Slider Fix
let index = 0;
const slides = document.querySelectorAll(".slide");
if(slides.length > 0) {
    setInterval(() => {
      slides.forEach(s => s.classList.remove("active"));
      index = (index + 1) % slides.length;
      slides[index].classList.add("active");
    }, 4000);
}

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
  .catch(() => {});

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

</body>
</html>
<?php ob_end_flush(); ?>