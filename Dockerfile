# EcoRide — Image Docker (PHP 8.2 + Apache + extensions MongoDB/PDO MySQL)
# Usage local : docker compose up   |   Déploiement : Render / Railway / Fly.io
FROM php:8.2-apache

# Dépendances système + extensions PHP
RUN apt-get update && apt-get install -y \
        libssl-dev libcurl4-openssl-dev pkg-config zip unzip git \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# DocumentRoot vers /public (front controller)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installation des dépendances (cache layer)
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copie du code applicatif
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# Port (Render / Railway injectent $PORT)
ENV PORT=10000
RUN sed -ri -e 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri -e 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/*.conf

EXPOSE 10000

CMD ["apache2-foreground"]
