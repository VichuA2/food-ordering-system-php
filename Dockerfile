FROM public.ecr.aws/docker/library/php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=public.ecr.aws/docker/library/composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

COPY food-app/ .

COPY food-app/apache.conf /etc/apache2/sites-available/000-default.conf

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts

RUN npm install && npm run build

RUN mkdir -p storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN a2enmod rewrite

EXPOSE 80

CMD ["sh","-c","php artisan config:clear && php artisan migrate --force && apache2-foreground"]
