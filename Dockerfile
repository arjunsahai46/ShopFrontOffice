FROM php:8.2-apache

# Installation des extensions nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Active mod_rewrite pour MVC
RUN a2enmod rewrite

# Copie d'abord composer.json et composer.lock pour optimiser le cache Docker
COPY composer.json composer.lock* /var/www/html/

# Installation des dépendances Composer (avant de copier tout le code)
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction || echo "Composer install failed, continuing..."

# Copie du reste du code dans le container
COPY . /var/www/html

# Donne les droits
RUN chown -R www-data:www-data /var/www/html

# Configure le VirtualHost si besoin
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf