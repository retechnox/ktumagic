<?php
// footer.php - comprehensive premium footer
?>
<style>
.premium-footer {
    background: var(--footer-bg);
    color: var(--footer-text);
    padding: 80px 0 40px;
    margin-top: 100px;
    border-top: 1px solid var(--footer-border);
}

.premium-footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 30px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: 60px;
    align-items: start;
}

.footer-brand .logo {
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 24px;
    display: inline-block;
    font-family: 'Sora', sans-serif;
    background: linear-gradient(to right, #60a5fa, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-decoration: none;
}

.footer-desc {
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 32px;
    color: var(--text-muted);
    max-width: 320px;
}

.footer-heading {
    color: var(--footer-heading);
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 28px;
    font-family: 'Sora', sans-serif;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 14px;
}

.footer-links a {
    color: var(--footer-text);
    text-decoration: none;
    font-size: 14.5px;
    transition: all 0.2s ease;
    display: inline-block;
    opacity: 0.85;
}

.footer-links a:hover {
    color: var(--footer-heading);
    transform: translateX(4px);
    opacity: 1;
}

.footer-bottom {
    margin-top: 80px;
    padding-top: 32px;
    border-top: 1px solid var(--footer-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: var(--text-muted);
}

.social-links {
    display: flex;
    gap: 12px;
}

.social-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: #94a3b8;
}

.social-icon:hover {
    background: #2563EB;
    border-color: #2563EB;
    color: white;
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
}

@media (max-width: 1024px) {
    .footer-grid {
        grid-template-columns: 1fr 1fr;
        gap: 48px;
    }
}

@media (max-width: 640px) {
    .premium-footer { padding: 60px 0 40px; }
    .footer-grid { grid-template-columns: 1fr; gap: 40px; text-align: center; }
    .footer-desc { margin-left: auto; margin-right: auto; }
    .footer-brand { display: flex; flex-direction: column; align-items: center; }
    .social-links { justify-content: center; }
    .footer-bottom {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
</style>

</main>
<footer class="premium-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="logo">KTU Magic</a>
            <p class="footer-desc">
                KTU Magic is an all-in-one academic support platform created to help KTU students make their academic journey easier, smarter, and more organized.
            </p>
            <div class="social-links">
                <a href="https://wa.me/XXXXXXXXXX" class="social-icon" aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467"/></svg>
                </a>
                <a href="#" class="social-icon" aria-label="Telegram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                </a>
                <a href="#" class="social-icon" aria-label="Instagram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                </a>
                <a href="#" class="social-icon" aria-label="LinkedIn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
            </div>
        </div>

        <div>
            <h4 class="footer-heading">Quick Links</h4>
            <ul class="footer-links">
                <li><a href="view_scheme.php">Academic Schemes</a></li>
                <li><a href="pyq.php">PYQ Search</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="#">Text Books</a></li>
                <li><a href="#">Syllabus</a></li>
            </ul>
        </div>

        <div>
            <h4 class="footer-heading">Resources</h4>
            <ul class="footer-links">
                <li><a href="#">KTU Updates</a></li>
                <li><a href="#">Internships</a></li>
                <li><a href="#">Tuitions</a></li>
                <li><a href="#">Question Banks</a></li>
            </ul>
        </div>

        <div>
            <h4 class="footer-heading">Support</h4>
            <ul class="footer-links">
                <li><a href="#">Help Center</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="#">Upload Notes</a></li>
                <li><a href="login.php">Admin Login</a></li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <div>
            © <?= date('Y') ?> KTU Magic. All rights reserved. 
            <span style="margin-left: 10px; color: #475569;">Trusted by 40k+ Students ⚡️</span>
        </div>
        <div>
            Built with ❤️ for KTU Students
        </div>
    </div>
</footer>
