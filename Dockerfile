# --------------------------------------------------
# PHP 8.2 + Apache
# --------------------------------------------------
FROM php:8.2-apache

# --------------------------------------------------
# System dependencies + PostgreSQL support
# --------------------------------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# Enable Apache rewrite
# --------------------------------------------------
RUN a2enmod rewrite

# --------------------------------------------------
# Set Apache document root to /frontend
# --------------------------------------------------
ENV APACHE_DOCUMENT_ROOT=/var/www/html/frontend

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# --------------------------------------------------
# Working directory
# --------------------------------------------------
WORKDIR /var/www/html

# --------------------------------------------------
# Copy application files
# --------------------------------------------------
COPY . /var/www/html

# --------------------------------------------------
# Uploads: backend owns them, frontend links to them
# --------------------------------------------------
RUN mkdir -p /var/www/html/backend/uploads \
    && rm -rf /var/www/html/frontend/uploads \
    && ln -s /var/www/html/backend/uploads /var/www/html/frontend/uploads

# --------------------------------------------------
# Permissions (safe defaults)
# --------------------------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/backend/uploads || true

# --------------------------------------------------
# Expose HTTP
# --------------------------------------------------
EXPOSE 80
