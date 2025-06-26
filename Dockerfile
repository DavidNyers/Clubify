# =================================================================
# Dockerfile für Laravel auf Render.com (V6 - FINAL OVERRIDE FIX)
# =================================================================

# --- Stufe 1: Backend-Abhängigkeiten ---
FROM php:8.2-apache as vendor
RUN apt-get update && apt-get install -y libonig-dev libzip-dev libpq-dev unzip && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-scripts

# --- Stufe 2: Frontend-Assets ---
FROM node:18 as frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Stufe 3: Finale Production-Umgebung ---
FROM php:8.2-apache
RUN apt-get update && apt-get install -y libonig-dev libzip-dev libpq-dev unzip && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Konfiguriere Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# HIER IST DIE NEUE, ENTSCHEIDENDE ZEILE: Erlaube .htaccess Dateien
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

RUN a2enmod rewrite
WORKDIR /var/www/html

# Kopiere Code & Assets
COPY . .
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Optimiere Laravel
RUN php artisan config:cache && php artisan route:cache

# Setze Dateiberechtigungen
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache

# Gib den Port frei
EXPOSE 80