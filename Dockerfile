FROM php:8.2-apache

# Installation des extensions nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Active mod_rewrite pour MVC
RUN a2enmod rewrite

# Copie du code dans le container
COPY . /var/www/html

# Donne les droits
RUN chown -R www-data:www-data /var/www/html

# Configure le VirtualHost si besoin
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf