# ─── Stage 1: Build Node.js WebSocket server deps ────────────────────────────
FROM node:20-alpine AS ws-builder
WORKDIR /ws
COPY ws_server/package*.json ./
RUN npm ci --omit=dev

# ─── Stage 2: Final image (Nginx + PHP-FPM + Node) ──────────────────────────
FROM php:8.1-fpm

# Install system packages
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev \
    zip unzip git curl \
    nginx supervisor nodejs \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# ── Nginx config ──────────────────────────────────────────────────────────
COPY nginx.conf /etc/nginx/nginx.conf

# ── Node WebSocket server ──────────────────────────────────────────────────
WORKDIR /ws
COPY ws_server/ ./
COPY --from=ws-builder /ws/node_modules ./node_modules

# ── PHP application ────────────────────────────────────────────────────────
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# ── Supervisor: manage nginx, php-fpm, and node ───────────────────────────
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]