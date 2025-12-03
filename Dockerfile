FROM php:8.1-apache

# Install OS dependencies (including git, curl, and common dev libraries)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer (PHP Dependency Manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working dir
WORKDIR /var/www/html

# Copy code
COPY . /var/www/html/

# Install PHP dependencies if needed (e.g., if you have a composer.json)
# RUN composer install --no-dev --prefer-dist

# Permissions (Important for Render)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
