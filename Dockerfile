# Use an official PHP runtime as a parent image
FROM php:8.2.3-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    pdo \
    sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Set the DocumentRoot
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy project files to working directory
COPY . .

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 755 /var/www/html/storage

# Install dependencies
RUN composer install

# Expose port 80, 8080 and 443
EXPOSE 80
EXPOSE 8080
EXPOSE 443

# Copy startup script
COPY startup.sh /usr/local/bin/

# Set permissions on startup script
RUN chmod +x /usr/local/bin/startup.sh

# Set startup script as default command
CMD ["/usr/local/bin/startup.sh"]