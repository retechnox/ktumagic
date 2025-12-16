<?php include 'db.php'; ?>
<?php
header("Cache-Control: no-transform");
header("Content-Encoding: none");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>KTU Magic – Notes & Resources</title>

<!-- Google Font: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>

/* ======================= GLOBAL ======================= */
body {
  font-family: 'Inter', sans-serif;
  background: #f7f8fa;
  color: #1a1a1a;
  margin: 0;
  padding: 0;
}

.container {
  width: min(1200px, 90%);
  margin: auto;
}

/* ======================= NAVBAR ======================= */
nav {
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  padding: 16px 0;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 50;
}

.nav-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* ======================= ALERT ======================= */
.alert-bar {
  margin-top: 80px;
  background: #2563EB;
  color: white;
  padding: 10px 0;
}

.marquee {
  overflow: hidden;
  white-space: nowrap;
  animation: marquee 18s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}

/* ======================= HERO ======================= */
.hero {
  text-align: center;
  padding: 80px 0 40px;
}

.hero h1 {
  font-size: 58px;
  font-weight: 800;
  background: linear-gradient(to right, #2563EB, #9333EA);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero p {
  max-width: 600px;
  margin: 10px auto;
  font-size: 18px;
  color: #555;
}

/* ======================= SLIDER ======================= */
.slider {
  position: relative;
  width: 100%;
  height: 350px;
  overflow: hidden;
  border-radius: 18px;
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 1s ease;
}

.slide.active { opacity: 1; }
.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ======================= ICON GRID ======================= */
.icon-grid {
  margin-top: 30px;
  display: grid;
  grid-template-columns: repeat(4,1fr);
  gap: 16px;
}

.icon-grid img {
  width: 100%;
  aspect-ratio: 1/1;
  object-fit: cover;
  border-radius: 12px;
  transition: 0.25s;
}

.icon-grid img:hover {
  transform: translateY(-4px);
}

/* ======================= CONTENT WRAPPER ======================= */
.main-flex {
  margin-top: 60px;
  display: flex;
  gap: 30px;
}

/* LEFT = 80% */
.left {
  width: 80%;
}

/* RIGHT = 20% */
.right {
  width: 20%;
  position: sticky;
  top: 100px;
  height: max-content;
}

/* ======================= SPONSORED CARD ======================= */
.sponsor-card {
  background: linear-gradient(to bottom right,#eef4ff,#dce3ff);
  padding: 20px;
  border-radius: 18px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.sponsor-card img {
  width: 100%;
  border-radius: 12px;
  margin-top: 10px;
}

.sponsor-button {
  background: #2563EB;
  color: white;
  text-align: center;
  padding: 12px;
  border-radius: 8px;
  margin-top: 15px;
  display: block;
  font-weight: 600;
  text-decoration: none;
}

/* ======================= CARD UI ======================= */
.card {
  background: white;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: 0.3s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card img {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
}

.card-body {
  padding: 18px;
}

.badge {
  display: inline-block;
  font-size: 12px;
  background: #dbeafe;
  color: #1e40af;
  padding: 5px 10px;
  border-radius: 6px;
  margin-right: 6px;
}

/* ======================= GRID ======================= */
.course-grid, .scheme-grid {
  display: grid;
  gap: 22px;
}

.course-grid {
  grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
}

.scheme-grid {
  grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
}

/* ======================= SCHEME FIX ======================= */
.scheme-card {
  min-height: 330px;
}

/* ======================= MODAL ======================= */
.modal-bg {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: none;
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(5px);
  z-index: 999;
}

.modal-box {
  background: white;
  padding: 25px;
  border-radius: 14px;
  width: 320px;
  text-align: center;
}

/* ======================= RESPONSIVE ======================= */
@media (max-width: 1024px) {
  .main-flex { flex-direction: column; }
  .left, .right { width: 100%; }
  .right { position: static; }
}

@media (max-width: 700px) {
  .hero h1 { font-size: 40px; }
  .icon-grid { grid-template-columns: repeat(2,1fr); }
}
</style>

</head>

<body>

<!-- ================= NAV ================= -->
<nav>
  <div class="container nav-inner">
    <div style="font-size:24px;font-weight:700;background:linear-gradient(to right,#2563EB,#9333EA);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
      KTU Magic
    </div>
    <div>
      <a href="view_scheme.php">View Notes</a> |
      <a href="#courses">Courses</a>
    </div>
  </div>
</nav>

<!-- ================= ALERT ================= -->
<div class="alert-bar">
  <div class="container">
    <span style="background:white;color:#2563EB;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">Alerts</span>
    <div class="marquee">2024 Scheme Updated • New Courses Added • Notes Uploading Weekly • Question Banks Refreshed</div>
  </div>
</div>

<!-- ================= HERO ================= -->
<section class="hero fade-up">
  <h1>Welcome to KTU Magic</h1>
  <p>Your one-stop destination for <strong>KTU notes, branches, courses & schemes</strong>.</p>
</section>

<!-- ================= SLIDER ================= -->
<div class="container">
  <div class="slider" id="slider">
    <div class="slide active"><img src="assets/slider1.jpg" /></div>
    <div class="slide"><img src="assets/slider2.jpg" /></div>
  </div>
</div>

<!-- ================= ICON GRID ================= -->
<div class="container">
  <div class="icon-grid">
    <?php for($i=1;$i<=8;$i++): ?>
      <img src="assets/<?= $i ?>.jpg" />
    <?php endfor; ?>
  </div>
</div>

<!-- SPACE FIX -->
<div style="margin-top:50px;"></div>

<!-- ================= MAIN + SIDEBAR ================= -->
<div class="container main-flex">

  <!-- LEFT 80% -->
  <div class="left">

    <!-- COURSES -->
    <h2 id="courses" style="font-size:32px;font-weight:700;margin-bottom:20px;">Latest Courses</h2>

    <div class="course-grid">
      <?php
      $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC LIMIT 12")->fetchAll();
      $DEFAULT_IMG = "assets/default_course.jpg";
      foreach ($courses as $c):
        $img = (!empty($c['image_path'])) ? $c['image_path'] : $DEFAULT_IMG;
      ?>
      <a href="view_link.php?course_id=<?= $c['id'] ?>" class="card fade-el">
        <img src="<?= $img ?>" />
        <div class="card-body">
          <h3 style="font-size:20px;font-weight:600;"><?= htmlspecialchars($c['name']) ?></h3>
          <p style="font-size:14px;color:#555;margin-top:6px;">Semester <?= $c['semester'] ?></p>

          <div style="margin-top:10px;">
            <span class="badge">Branch <?= $c['branch_id'] ?></span>
            <span class="badge" style="background:#f3e8ff;color:#7e22ce;">Scheme <?= $c['scheme_id'] ?></span>
          </div>

          <p style="color:#2563EB;margin-top:12px;font-size:14px;">Open Course →</p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- SCHEMES -->
    <h2 style="font-size:32px;font-weight:700;margin:40px 0 20px;">Schemes</h2>

    <div class="scheme-grid">

      <a href="view_branch.php?scheme_id=3" class="card scheme-card fade-el">
        <img src="assets/2019/1.jpg">
        <div class="card-body">
          <h3 style="font-size:22px;font-weight:600;">2019 Scheme</h3>
          <p style="color:#555;margin-top:5px;">Browse Branches →</p>
        </div>
      </a>

      <a href="view_branch.php?scheme_id=4" class="card scheme-card fade-el">
        <img src="assets/2025/1.jpg">
        <div class="card-body">
          <h3 style="font-size:22px;font-weight:600;">2025 Scheme</h3>
          <p style="color:#555;margin-top:5px;">Browse Branches →</p>
        </div>
      </a>

    </div>
  </div>

  <!-- RIGHT 20% (SPONSORED) -->
  <div class="right fade-el">
    <div class="sponsor-card">
      <h3 style="font-size:20px;font-weight:700;color:#2563EB;">📢 Sponsored</h3>
      <p style="font-size:14px;margin-top:6px;color:#555;">Promote your course or notes here and reach thousands of students.</p>
      <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=900&q=60" />
      <a href="#" class="sponsor-button">Advertise Here</a>
    </div>
  </div>

</div>

<!-- ================= MODAL ================= -->
<div id="socialModal" class="modal-bg">
  <div class="modal-box">
    <h2 style="font-size:22px;font-weight:700;">Connect With Us</h2>
    <div style="display:flex;flex-direction:column;gap:10px;margin-top:15px;">
      <a class="sponsor-button" style="background:#d946ef;">Instagram</a>
      <a class="sponsor-button" style="background:#16a34a;">WhatsApp</a>
      <a class="sponsor-button" style="background:#0ea5e9;">Telegram</a>
      <a class="sponsor-button" style="background:#1d4ed8;">Facebook</a>
    </div>
    <button onclick="closeModal()" style="margin-top:15px;background:none;border:none;color:#777;cursor:pointer;">Close</button>
  </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>

// ================== GSAP ==================
document.addEventListener("DOMContentLoaded", () => {
  gsap.from(".fade-up", { opacity: 0, duration: 1 });
  gsap.from(".fade-el", { opacity: 0, duration: 1, stagger: 0.12 });
});

// ================== SLIDER ==================
let index = 0;
setInterval(() => {
  const slides = document.querySelectorAll("#slider .slide");
  slides.forEach(s => s.classList.remove("active"));
  slides[index].classList.add("active");
  index = (index + 1) % slides.length;
}, 3000);

// ================== MODAL ==================
function closeModal() {
  document.getElementById("socialModal").style.display = "none";
}

if (!localStorage.getItem("socialShown")) {
  setTimeout(() => {
    document.getElementById("socialModal").style.display = "flex";
  }, 1500);
  localStorage.setItem("socialShown", "true");
}

</script>

</body>
</html>
