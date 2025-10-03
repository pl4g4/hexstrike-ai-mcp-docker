FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install SQLite3 extension
RUN apt-get update && \
    apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo_sqlite && \
    rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY ./app /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Initialize database on container start
RUN php /var/www/html/db.php

EXPOSE 80