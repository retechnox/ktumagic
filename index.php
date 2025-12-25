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
  margin: 40px auto;
  justify-content: center;  /* center items horizontally */
  display: grid;
  grid-auto-flow: row;          /* fill columns, not rows */
  grid-template-columns: repeat(4, 150px); /* EXACTLY 2 rows */
  grid-auto-columns: 150px;        /* width of each item */
  gap: 20px;
  max-width: 100%;
  padding-bottom: 10px;
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
.scheme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 50px; }

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
  .nav-links a:not(.upload-cta) { display: none; }
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

/* ======================= MODAL ======================= */
.modal-bg {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85);
  backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 5000;
}
.modal-box {
  background: white; padding: 25px; border-radius: 20px; width: min(350px, 90%); text-align: center;
}
.social-link {
  display: block; padding: 10px; margin: 6px 0; border-radius: 10px;
  color: white; text-decoration: none; font-weight: 700; font-size: 14px;
}

html, body {
  max-width: 100%;
  overflow-x: hidden;
}


</style>
</head>

<body>

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

<div class="alert-bar">
  <div class="container">
    <div class="marquee">✨ 2024 Scheme Updated • New Courses Added Weekly • Download Verified Question Banks • Join our WhatsApp Community for Instant Alerts ✨</div>
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
          <p style="color:var(--primary-blue); font-size:13px; margin-top:8px; font-weight:600;">BROWSE  →</p>
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
      <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=400" style="width:100%; border-radius:12px; margin-bottom:10px;">
      <p style="font-size:13px; color:#64748b;">Support our platform to keep resources free for everyone.</p>
    </div>
  </div>
</div>

<div id="socialModal" class="modal-bg">
  <div class="modal-box">
    <h2 style="font-family:'Sora'; font-size:20px; margin:0 0 10px;">Join the Squad 🤝</h2>
    <p style="color:#64748b; font-size:13px; margin-bottom:15px;">Get the fastest KTU alerts directly on your phone.</p>
    <a class="social-link" style="background:#25D366;">WhatsApp Community</a>
    <a class="social-link" style="background:#0088cc;">Telegram Channel</a>
    <a class="social-link" style="background:#E1306C;">Instagram</a>
    <button onclick="closeModal()" style="margin-top:10px; border:none; background:none; color:#94a3b8; cursor:pointer; font-size:12px;">Maybe Later</button>
  </div>
</div>


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

function closeModal() {
  document.getElementById("socialModal").style.display = "none";
}

window.addEventListener("load", () => {
  if (!localStorage.getItem("socialShown")) {
    setTimeout(() => {
      document.getElementById("socialModal").style.display = "flex";
    }, 2000);
    localStorage.setItem("socialShown", "1");
  }
});
</script>

</body>
</html>
<?php ob_end_flush(); ?>