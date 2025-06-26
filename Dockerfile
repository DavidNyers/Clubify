# =================================================================
# Dockerfile für Laravel auf Render.com (V7 - FINAL MYSQL FIX)
# =================================================================

# --- Stufe 1: Backend-Abhängigkeiten in einer korrekten PHP-Umgebung installieren ---
# Wir starten mit einem PHP-Apache-Image, um sicherzustellen, dass die Umgebung korrekt ist.
FROM php:8.2-apache as vendor

# Installiere System-Abhängigkeiten und alle benötigten PHP-Erweiterungen.
# Wichtig: pdo_mysql für Ihre Datenbank und exif für das Spatie-Paket.
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Installiere Composer global im Image.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setze das Arbeitsverzeichnis.
WORKDIR /app

# Kopiere nur die notwendigen Dateien, um den Docker-Cache zu nutzen.
COPY database/ database/
COPY composer.json composer.lock ./

# Installiere Pakete, aber überspringe die post-install-Skripte, um Fehler zu vermeiden.
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-scripts


# --- Stufe 2: Frontend-Assets bauen (mit Node.js und Vite) ---
# Diese Stufe kompiliert Ihr CSS und JavaScript.
FROM node:18 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build


# --- Stufe 3: Das finale Production-Image erstellen ---
# Wir starten wieder mit einem sauberen PHP-Apache-Image.
FROM php:8.2-apache

# Installiere die Laufzeit-Erweiterungen erneut für die finale Umgebung.
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Konfiguriere den Apache-Webserver korrekt.
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Erlaube .htaccess-Dateien, um die Laravel-Routen zu ermöglichen (Löst 404-Fehler).
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Aktiviere das Apache 'rewrite'-Modul.
RUN a2enmod rewrite

# Setze das finale Arbeitsverzeichnis.
WORKDIR /var/www/html

# Kopiere den gesamten Anwendungscode und die kompilierten Assets aus den vorherigen Stufen.
COPY . .
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Optimiere Laravel für die Produktion durch Caching.
RUN php artisan config:cache && php artisan route:cache

# Setze die finalen Dateiberechtigungen, um Server-Fehler zu vermeiden.
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache

# Gib den Port 80 für den Webserver frei.
EXPOSE 80