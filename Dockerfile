# Use the official PHP Apache image
FROM php:8.2-apache

# Copy all project files to Apache web root
COPY . /var/www/html/

# Expose port 80 (default HTTP port)
EXPOSE 80

# Apache runs automatically with this image, so no CMD needed
