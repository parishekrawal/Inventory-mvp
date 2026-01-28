FROM php:8.2-apache

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libonig-dev libzip-dev zip npm default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring zip \
    && a2enmod rewrite

# Apache config: Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . .

# Make wait-for-db.sh executable
RUN chmod +x wait-for-db.sh

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port for Render
EXPOSE 80

# Use the wait-for-db script to start the app
CMD ["./wait-for-db.sh"]
