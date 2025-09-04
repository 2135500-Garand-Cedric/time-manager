# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, good for pretty URLs)
RUN a2enmod rewrite

# Copy project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set permissions so Apache can read Windows-mounted files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
