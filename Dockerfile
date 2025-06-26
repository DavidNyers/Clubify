# =================================================================
# Dockerfile für eine moderne Laravel-Anwendung auf Render.com (V3 - FINAL)
# =================================================================

# --- Stufe 1: Backend-Abhängigkeiten in einer korrekten PHP-Umgebung installieren ---
# Wir starten jetzt mit dem PHP-Image und installieren Composer manuell.
# Das stellt sicher, dass die PHP-Erweiterungen während `composer install` verfügbar sind.
FROM php:8.2-apache as vendor

# Installiere System-Abhängigkeiten und die benötigten PHP-Erweiterungen (inkl. exif!)
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Installiere Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader


# --- Stufe 2: Frontend-Assets bauen (mit Node.js und Vite) ---
# Diese Stufe bleibt unverändert.
FROM node:18 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build


# --- Stufe 3: Das finale Production-Image erstellen ---
# Wir starten wieder mit einem sauberen PHP-Image.
FROM php:8.2-apache

# Installiere die Erweiterungen erneut für die finale Laufzeitumgebung.
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Konfiguriere Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

WORKDIR /var/www/html

# Kopiere den Anwendungscode und die gebauten Assets aus den vorherigen Stufen.
COPY . .
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Setze die notwendigen Berechtigungen.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Gib den Port frei.
EXPOSE 80