const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const webPush = require('web-push');
const mysql = require('mysql2/promise');
const dotenv = require('dotenv');
const path = require('path');

// Load .env from parent directory
dotenv.config({ path: path.resolve(__dirname, '../.env') });

// ── Config from environment ──────────────────────────────────────────────────
const PORT = parseInt(process.env.WS_PORT || '8080', 10);
const ADMIN_SECRET = process.env.WS_ADMIN_SECRET || 'magic_ktu_admin_secret_2026';
const APP_ENV = process.env.APP_ENV || 'development';

// Web Push Config
const VAPID_PUBLIC = process.env.VAPID_PUBLIC_KEY;
const VAPID_PRIVATE = process.env.VAPID_PRIVATE_KEY;

if (VAPID_PUBLIC && VAPID_PRIVATE) {
    webPush.setVapidDetails(
        'mailto:admin@ktumagic.in',
        VAPID_PUBLIC,
        VAPID_PRIVATE
    );
    console.log('[Push] VAPID details set ✓');
} else {
    console.warn('[Push] VAPID keys missing in .env!');
}

// DB Config
const dbConfig = {
    host: process.env.DB_HOST,
    port: parseInt(process.env.DB_PORT || '3306', 10),
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
};

const app = express();
app.use(express.json());

const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

let connectedClients = 0;

wss.on('connection', (ws, req) => {
    connectedClients++;
    ws.isAlive = true;
    ws.on('pong', () => { ws.isAlive = true; });
    ws.on('close', () => { connectedClients--; });
});

// Heartbeat
const interval = setInterval(() => {
    wss.clients.forEach((ws) => {
        if (ws.isAlive === false) return ws.terminate();
        ws.isAlive = false;
        ws.ping();
    });
}, 30000);

wss.on('close', () => clearInterval(interval));

// ── Broadcast Logic ─────────────────────────────────────────────────────────
app.post('/broadcast', async (req, res) => {
    const { secret, title, body, link } = req.body;

    if (!secret || secret !== ADMIN_SECRET) {
        return res.status(403).json({ error: 'Unauthorized' });
    }

    if (!title || !body) {
        return res.status(400).json({ error: 'Title and body are required.' });
    }

    const payload = JSON.stringify({ type: 'notification', title, body, link: link || '' });

    // 1. WebSocket Broadcast (Live)
    let wsCount = 0;
    wss.clients.forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(payload);
            wsCount++;
        }
    });

    // 2. Web Push Broadcast (Background)
    let pushCount = 0;
    let pushErrors = 0;

    try {
        const connection = await mysql.createConnection(dbConfig);
        const [rows] = await connection.execute('SELECT * FROM push_subscriptions');
        await connection.end();

        const pushPromises = rows.map(sub => {
            const pushSubscription = {
                endpoint: sub.endpoint,
                keys: {
                    p256dh: sub.p256dh,
                    auth: sub.auth
                }
            };

            return webPush.sendNotification(pushSubscription, payload)
                .then(() => { pushCount++; })
                .catch(err => {
                    if (err.statusCode === 404 || err.statusCode === 410) {
                        // Sub expired or removed, should delete from DB in real app
                        pushErrors++;
                    }
                    console.error('[Push] Error for endpoint:', sub.endpoint, err.message);
                });
        });

        await Promise.all(pushPromises);
    } catch (dbErr) {
        console.error('[DB] Error fetching subscriptions:', dbErr);
    }

    console.log(`[HTTP] Broadcast "${title}" sent to ${wsCount} WS clients and ${pushCount} Push devices.`);
    res.json({ success: true, deliveredWS: wsCount, deliveredPush: pushCount, pushErrors });
});

app.get('/health', (_req, res) => res.json({ status: 'ok', env: APP_ENV, clients: connectedClients }));
app.get('/', (_req, res) => res.send(`KTU Magic WS Server [${APP_ENV}] – ${connectedClients} connected clients.`));

server.listen(PORT, () => console.log(`[WS] Server listening on port ${PORT}`));
