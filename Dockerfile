# 1️⃣ Use official PHP 8.2 with Apache
FROM php:8.2-apache

# 2️⃣ Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libonig-dev \
    libzip-dev \
    zip \
    npm \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring zip \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3️⃣ Set working directory inside container
WORKDIR /var/www/html

# 4️⃣ Copy project files into container
COPY . .

# 5️⃣ Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6️⃣ Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# 7️⃣ Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8️⃣ Expose port 80
EXPOSE 80

# 9️⃣ Start Apache in foreground
CMD ["apache2-foreground"]
