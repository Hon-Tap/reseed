
# Official PHP with Apache
FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set Apache document root to /frontend
ENV APACHE_DOCUMENT_ROOT=/var/www/html/frontend

# Update Apache config to use new document root
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . /var/www/html

# Permissions for writable directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads /var/www/html/logs || true

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . /var/www/html

# Create uploads directory and expose it to frontend (runtime-safe)
RUN mkdir -p /var/www/html/backend/uploads \
    && rm -rf /var/www/html/frontend/uploads \
    && ln -s /var/www/html/backend/uploads /var/www/html/frontend/uploads

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/backend/uploads /var/www/html/logs || true

EXPOSE 80
