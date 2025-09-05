# Use the official PHP image with Apache
FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite (optional, good for pretty URLs)
RUN a2enmod rewrite

# Copy project files into the container
COPY ./src /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set permissions so Apache can read Windows-mounted files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
