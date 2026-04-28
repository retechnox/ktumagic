<?php
// footer.php - comprehensive premium footer
include_once __DIR__ . '/db.php';
$jsonData = @file_get_contents(__DIR__ . '/data/data.json') ?: '{}';
$data = json_decode($jsonData, true);
$contact = $data['contact'] ?? [];
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
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }

    /* Brand-coloured icons – visible in both dark & light mode */
    .social-icon.si-whatsapp {
        background: #25D366;
        color: #fff;
    }

    .social-icon.si-telegram {
        background: #229ED9;
        color: #fff;
    }

    .social-icon.si-instagram {
        background: #E1306C;
        color: #fff;
    }

    .social-icon.si-linkedin {
        background: #0A66C2;
        color: #fff;
    }

    .social-icon:hover {
        filter: brightness(1.15);
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
    }

    @media (max-width: 1024px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 48px;
        }
    }

    @media (max-width: 640px) {
        .premium-footer {
            padding: 60px 0 40px;
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            text-align: center;
        }

        .footer-desc {
            margin-left: auto;
            margin-right: auto;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .social-links {
            justify-content: center;
        }

        .footer-bottom {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
    }

    /* Global Floating Actions */
    .floating-actions {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 1000;
        pointer-events: none; /* Let clicks pass through gaps */
    }

    .floating-btn {
        pointer-events: auto;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
    }

    .floating-btn:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }

    .floating-btn.instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }

    .floating-btn.whatsapp {
        background: #25D366;
    }

    .floating-btn svg {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
    }

    @media (max-width: 768px) {
        .floating-actions {
            bottom: 25px;
            right: 20px;
            gap: 12px;
        }
        .floating-btn {
            width: 54px;
            height: 54px;
        }
    }
</style>

</main>
<footer class="premium-footer"> 
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="logo" style="display:block; margin-bottom: 24px;">
                <img src="assets/logooo.webp" alt="KTU Magic"
                    style="height: 150px; width: auto; filter: drop-shadow(0 2px 12px rgba(0,0,0,0.25));">
            </a>
            <p class="footer-desc">
                KTU Magic is an all-in-one academic support platform created to help KTU students make their academic
                journey easier, smarter, and more organized.
            </p>
            <div class="social-links">
                <a href="<?= $contact['whatsapp_main'] ?? '#' ?>" class="social-icon si-whatsapp"
                    aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
                    </svg>
                </a>
                <a href="<?= $contact['telegram'] ?? '#' ?>" class="social-icon si-telegram" aria-label="Telegram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20.665 3.717l-17.73 6.837c-1.213.486-1.203 1.163-.222 1.462l4.552 1.42 1.566 4.802c.188.518.093.723.475.723.296 0 .43-.135.594-.293l2.394-2.327 4.98 3.68c.918.506 1.577.246 1.807-.85l3.268-15.396c.335-1.343-.513-1.952-1.394-1.56z" />
                    </svg>
                </a>
                <a href="<?= $contact['instagram'] ?? '#' ?>" class="social-icon si-instagram" aria-label="Instagram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                    </svg>
                </a>
                <a href="<?= $contact['youtube'] ?? '#' ?>" class="social-icon si-linkedin" aria-label="YouTube">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                    </svg>
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
                <li><a href="<?= $contact['whatsapp_main'] ?? '#' ?>" target="_blank">WhatsApp Support</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="https://forms.gle/fZAdbW2EkLJBFXoBA" target="_blank">Upload Notes</a></li>
                <li><a href="login.php">Admin Login</a></li>
            </ul>
        </div>
    </div>
    <br />
    <div class="container footer-bottom">
        <div>
            ©
            <?= date('Y')?> KTU Magic. All rights reserved. <br />
            <span style="margin-left: 10px; color: var(--text-subtle);">Trusted by 50k+ Students ⚡️</span>
        </div>
        <!-- <div>
            Built with ❤️ for KTU Students
        </div> -->
    </div>

    <!-- Push Notification Prompt -->
    <div id="pushPrompt"
        style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.1); padding: 16px 24px; border-radius: 16px; z-index: 9999; flex-direction: column; gap: 12px; min-width: 320px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div
                style="background: rgba(37, 99, 235, 0.1); color: var(--primary-blue); width: 40px; height: 40px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
            <div>
                <h4 style="margin: 0; font-family: 'Sora', sans-serif; font-size: 15px; color: var(--text-primary);">
                    Enable Notifications</h4>
                <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-secondary);">Get real-time KTU updates
                    instantly.</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 4px;">
            <button id="btnNotNow"
                style="flex: 1; padding: 8px; background: transparent; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-secondary); font-weight: 600; cursor: pointer;">Not
                Now</button>
            <button id="btnEnablePush"
                style="flex: 1; padding: 8px; background: var(--primary-blue); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">Enable</button>
        </div>
    </div><!-- /.pushPrompt -->

    <!-- Global Floating Buttons -->
    <div class="floating-actions">
        <!-- Instagram Above WhatsApp -->
        <a href="<?= $contact['instagram'] ?? '#' ?>" target="_blank" class="floating-btn instagram" aria-label="Instagram">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
            </svg>
        </a>
        <a href="https://chat.whatsapp.com/Lk4IJeebv8s6xIpqg2cT1H?mode=gi_t" target="_blank" class="floating-btn whatsapp" aria-label="WhatsApp">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.445 0 .01 5.437 0 12.045c0 2.112.552 4.171 1.594 5.96L0 24l6.135-1.61a11.817 11.817 0 005.908 1.569h.005c6.608 0 12.046-5.436 12.049-12.044a11.758 11.758 0 00-3.417-8.467" />
            </svg>
        </a>
    </div>


    <script>
        // ── Derive base path (works on localhost/ktumagic AND ktumagic.in root) ────
        const _isProduction = (location.hostname === 'ktumagic.in' || location.hostname === 'www.ktumagic.in');
        const _basePath = _isProduction ? '' : '/ktumagic';
        const _iconPath = _basePath + '/assets/logooo.webp';
        const _vapidPublicKey = '<?= getenv("VAPID_PUBLIC_KEY") ?>';

        // Helper to convert VAPID key
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // ── Service Worker registration ──────────────────────────────────────────
        let _swReg = null;
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(_basePath + '/sw.js')
                .then(reg => {
                    _swReg = reg;
                    console.log('[SW] Registered:', reg.scope);
                    return reg.pushManager.getSubscription();
                })
                .then(sub => {
                    if (sub) console.log('[Push] User is already subscribed.');
                })
                .catch(err => console.error('[SW] Error:', err));
        }

        // ── Persistent notification via Service Worker ───────────────────────────
        function showPersistentNotification(title, body, link) {
            const notifOptions = {
                body: body, icon: _iconPath, badge: _iconPath,
                data: { link: link || '' }, tag: 'ktu-broadcast',
                requireInteraction: false, vibrate: [200, 100, 200]
            };

            if (Notification.permission === 'granted') {
                navigator.serviceWorker.ready
                    .then(reg => {
                        if (reg.active) {
                            reg.active.postMessage({
                                type: 'SHOW_NOTIFICATION',
                                title, body, link: link || '', icon: _iconPath
                            });
                        } else {
                            return reg.showNotification(title, notifOptions);
                        }
                    })
                    .catch(() => {
                        try { new Notification(title, notifOptions); } catch (e) { }
                    });
            }
            showToast(title, body, link);
        }

        // ── In-page toast ────────────────────────────────────────────────────────
        function showToast(title, body, link) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed; top: 70px; left: 50%; transform: translateX(-50%);
                background: var(--bg-card, #fff); color: var(--text-primary, #111);
                border: 1px solid var(--border-color, #e5e7eb);
                border-left: 4px solid #2563EB;
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                padding: 14px 18px; border-radius: 14px;
                width: min(380px, 90vw);
                font-family: 'Sora', sans-serif;
                z-index: 99999;
                animation: slideDownToast 0.35s cubic-bezier(0.34,1.56,0.64,1);
                cursor: ${link ? 'pointer' : 'default'};
            `;
            toast.innerHTML = `
                <style>@keyframes slideDownToast{from{transform:translateX(-50%) translateY(-30px);opacity:0}to{transform:translateX(-50%) translateY(0);opacity:1}}</style>
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <span style="font-size:20px;line-height:1;">🔔</span>
                    <div>
                        <div style="font-weight:700;font-size:14px;margin-bottom:3px;">${title}</div>
                        <div style="font-size:12.5px;opacity:0.75;line-height:1.5;">${body}</div>
                    </div>
                    <span id="toastClose" style="margin-left:auto;cursor:pointer;opacity:0.5;font-size:18px;line-height:1;">✕</span>
                </div>
            `;
            if (link) toast.onclick = () => window.open(link, '_blank');
            toast.querySelector('#toastClose').onclick = (e) => { e.stopPropagation(); toast.remove(); };
            document.body.appendChild(toast);
            setTimeout(() => { if (toast.parentNode) toast.remove(); }, 7000);
        }

        let _ws = null;
        function connectWebSocket() {
            if (_ws && _ws.readyState === WebSocket.OPEN) return;
            const wsUrl = _isProduction ? 'wss://ktumagic.in/ws' : 'ws://localhost:8080';
            _ws = new WebSocket(wsUrl);
            _ws.onopen = () => console.log('[WS] Connected ✓');
            _ws.onclose = () => setTimeout(connectWebSocket, 5000);
            _ws.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    if (data.type === 'notification') showPersistentNotification(data.title, data.body, data.link);
                } catch (e) { console.error('[WS] Error:', e); }
            };
        }

        window.requestPushPermission = async function() {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    connectWebSocket();
                    if (_swReg && _vapidPublicKey) {
                        const subscription = await _swReg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(_vapidPublicKey)
                        });
                        await fetch(_basePath + '/save_subscription.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(subscription)
                        });
                    }
                    localStorage.setItem('notifications_enabled', 'true');
                    updateNotificationUI();
                    return true;
                }
                return false;
            } catch (err) { return false; }
        };

        function updateNotificationUI() {
            const isEnabled = localStorage.getItem('notifications_enabled') === 'true' || 
                             (window.Notification && Notification.permission === 'granted');
            const navBell = document.getElementById('navNotificationBell');
            const sidebarItem = document.getElementById('sidebarNotificationItem');
            const heroBtn = document.getElementById('heroNotificationBtn');
            const prompt = document.getElementById('pushPrompt');
            if (isEnabled) {
                if (navBell) navBell.style.display = 'none';
                if (sidebarItem) sidebarItem.style.display = 'none';
                if (heroBtn) heroBtn.style.display = 'none';
                if (prompt) prompt.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const prompt = document.getElementById('pushPrompt');
            const btnNotNow = document.getElementById('btnNotNow');
            const btnEnablePush = document.getElementById('btnEnablePush');

            updateNotificationUI();
            if ('Notification' in window && Notification.permission === 'granted') {
                connectWebSocket();
            } else if (Notification.permission !== 'denied' && !localStorage.getItem('push_prompt_dismissed')) {
                setTimeout(() => { if (prompt && localStorage.getItem('notifications_enabled') !== 'true') prompt.style.display = 'flex'; }, 2000);
            }

            if (btnNotNow) {
                btnNotNow.onclick = () => {
                    localStorage.setItem('push_prompt_dismissed', 'true');
                    if (prompt) prompt.style.display = 'none';
                };
            }
            if (btnEnablePush) {
                btnEnablePush.onclick = async () => {
                    if (prompt) prompt.style.display = 'none';
                    await window.requestPushPermission();
                };
            }
        });
    </script>
</footer>