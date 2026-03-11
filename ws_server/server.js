const express = require('express');
const http = require('http');
const WebSocket = require('ws');

// ── Config from environment ──────────────────────────────────────────────────
const PORT = parseInt(process.env.WS_PORT || '8080', 10);
const ADMIN_SECRET = process.env.WS_ADMIN_SECRET || 'magic_ktu_admin_secret_2026';
const APP_ENV = process.env.APP_ENV || 'development';

console.log(`[WS] Starting in ${APP_ENV} mode on port ${PORT}`);

const app = express();
app.use(express.json());

const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

let connectedClients = 0;

wss.on('connection', (ws, req) => {
    connectedClients++;
    console.log(`[WS] Client connected from ${req.socket.remoteAddress}. Total: ${connectedClients}`);

    ws.isAlive = true;
    ws.on('pong', () => { ws.isAlive = true; });

    ws.on('close', () => {
        connectedClients--;
        console.log(`[WS] Client disconnected. Total: ${connectedClients}`);
    });
});

// Heartbeat – keep connections alive and prune dead ones
const interval = setInterval(() => {
    wss.clients.forEach((ws) => {
        if (ws.isAlive === false) return ws.terminate();
        ws.isAlive = false;
        ws.ping();
    });
}, 30000);

wss.on('close', () => clearInterval(interval));

// ── POST /broadcast – admin sends notification to all connected clients ──────
app.post('/broadcast', (req, res) => {
    const { secret, title, body, link } = req.body;

    if (!secret || secret !== ADMIN_SECRET) {
        console.warn('[HTTP] Unauthorized broadcast attempt.');
        return res.status(403).json({ error: 'Unauthorized' });
    }

    if (!title || !body) {
        return res.status(400).json({ error: 'Title and body are required.' });
    }

    const payload = JSON.stringify({ type: 'notification', title, body, link: link || '' });

    let count = 0;
    wss.clients.forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(payload);
            count++;
        }
    });

    console.log(`[HTTP] Broadcast "${title}" sent to ${count} clients.`);
    res.json({ success: true, deliveredTo: count });
});

app.get('/health', (_req, res) => res.json({ status: 'ok', env: APP_ENV, clients: connectedClients }));
app.get('/', (_req, res) => res.send(`KTU Magic WS Server [${APP_ENV}] – ${connectedClients} connected clients.`));

server.listen(PORT, () => console.log(`[WS] Server listening on port ${PORT}`));
