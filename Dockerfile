# =================================================================
# Dockerfile für eine moderne Laravel-Anwendung auf Render.com (V2)
# =================================================================

# --- Stufe 1: Backend-Abhängigkeiten installieren ---
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist --optimize-autoloader --no-dev


# --- Stufe 2: Frontend-Assets bauen (mit Node.js und Vite) ---
FROM node:18 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build


# --- Stufe 3: Das finale Production-Image erstellen ---
FROM php:8.2-apache

# Installiere System-Pakete und die von Laravel benötigten PHP-Erweiterungen.
# HIER IST DIE KORREKTUR: 'exif' wurde zur Liste hinzugefügt.
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql bcmath mbstring zip exif

# Konfiguriere Apache so, dass er auf den /public Ordner von Laravel zeigt.
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# Setze das Arbeitsverzeichnis für den Webserver.
WORKDIR /var/www/html

# Kopiere den gesamten Anwendungscode in das Image.
COPY . .

# Kopiere die installierten Abhängigkeiten aus den vorherigen Stufen.
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Setze die notwendigen Berechtigungen für Laravel.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Gib den Port frei, auf dem Apache lauscht.
EXPOSE 80