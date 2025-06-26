# =================================================================
# Dockerfile für eine moderne Laravel-Anwendung auf Render.com
# =================================================================

# --- Stufe 1: Backend-Abhängigkeiten installieren ---
# Wir verwenden ein offizielles Composer-Image, um die PHP-Pakete zu installieren.
# Das hält unser finales Image sauber, da Composer selbst nicht im Endprodukt landet.
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./

# Installiere nur die Production-Abhängigkeiten.
RUN composer install --no-interaction --no-scripts --prefer-dist --optimize-autoloader --no-dev


# --- Stufe 2: Frontend-Assets bauen (mit Node.js und Vite) ---
# Wir verwenden ein Node.js-Image, um die CSS/JS-Dateien zu kompilieren.
# Auch dieser Teil landet nicht im finalen Image.
FROM node:18 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install

# Kopiere den restlichen Code, damit Vite/NPM Zugriff auf alle Dateien hat.
COPY . .
# Führe den Build-Befehl aus, der in deiner package.json definiert ist.
RUN npm run build


# --- Stufe 3: Das finale Production-Image erstellen ---
# Wir starten mit einem offiziellen PHP-Image, das den Apache-Webserver enthält.
# Wähle hier die PHP-Version, die zu deinem Projekt passt (z.B. php:8.2-apache).
FROM php:8.2-apache

# Installiere System-Pakete und die von Laravel benötigten PHP-Erweiterungen.
# libpq-dev wird für die Verbindung zu PostgreSQL-Datenbanken benötigt.
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql bcmath mbstring zip

# Konfiguriere Apache so, dass er auf den /public Ordner von Laravel zeigt.
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktiviere das Apache 'rewrite' Modul für "schöne" URLs.
RUN a2enmod rewrite

# Setze das Arbeitsverzeichnis für den Webserver.
WORKDIR /var/www/html

# Kopiere den gesamten Anwendungscode in das Image.
COPY . .

# Kopiere die installierten Abhängigkeiten aus den vorherigen Stufen.
# Dies ist der Kern der Multi-Stage-Build-Strategie.
COPY --from=vendor /app/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/

# Setze die notwendigen Berechtigungen, damit Laravel in die storage- und cache-Ordner schreiben kann.
# Der Webserver läuft als 'www-data' Benutzer.
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Gib den Port frei, auf dem Apache lauscht. Render nutzt dies.
EXPOSE 80