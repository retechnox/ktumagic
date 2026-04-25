// KTU Magic Service Worker – handles persistent push notifications
// Keeps notifications in the OS notification panel until dismissed

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (e) => e.waitUntil(self.clients.claim()));

// Triggered from the page via postMessage when a WS broadcast arrives
self.addEventListener('message', (event) => {
    const { type, title, body, link, icon } = event.data || {};
    if (type !== 'SHOW_NOTIFICATION') return;

    event.waitUntil(
        self.registration.showNotification(title || 'KTU Magic', {
            body: body || '',
            icon: icon || '/ktumagic/assets/logooo.webp',
            badge: '/ktumagic/assets/logooo.webp',
            data: { link: link || '' },
            tag: 'ktu-broadcast',          // Replaces previous unread notification
            requireInteraction: false,     // Still dismissable but stays in panel
            vibrate: [200, 100, 200],
        })
    );
});

// Clicking the notification opens the attached link
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const link = event.notification.data?.link;
    if (link) {
        event.waitUntil(clients.openWindow(link));
    } else {
        // Focus the site if already open
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then((windowClients) => {
                    if (windowClients.length > 0) {
                        return windowClients[0].focus();
                    }
                    return clients.openWindow('/ktumagic/');
                })
        );
    }
});

// ── Background Push API Handler ──────────────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;
    
    try {
        const data = event.data.json();
        const { title, body, link, icon } = data;

        event.waitUntil(
            self.registration.showNotification(title || 'KTU Magic', {
                body: body || '',
                icon: icon || '/assets/logooo.webp',
                badge: '/assets/logooo.webp',
                data: { link: link || '' },
                tag: 'ktu-broadcast',
                requireInteraction: false,
                vibrate: [200, 100, 200],
            })
        );
    } catch (err) {
        console.error('[SW] Push error:', err);
    }
});
