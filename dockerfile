# Use official PHP image with Apache
FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpq-dev \
    ca-certificates \
    curl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && curl https://cacerts.digicert.com/DigiCertGlobalRootCA.crt.pem \
       -o /usr/local/share/ca-certificates/DigiCertGlobalRootCA.crt.pem \
    && update-ca-certificates \
    && ln -sf /usr/local/share/ca-certificates/DigiCertGlobalRootCA.crt.pem /etc/ssl/certs/DigiCertGlobalRootCA.pem

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set Apache document root to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Remove artisan cache commands (Render injects env vars at runtime)
# Avoid SQLite fallback during build
# If you ever need to clear caches, do it manually via Render shell or Start Command

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
