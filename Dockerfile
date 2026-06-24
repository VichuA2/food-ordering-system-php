FROM public.ecr.aws/docker/library/php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN apt-get update && apt-get install -y \
    wget \
    unzip

RUN wget -q https://s3.amazonaws.com/amazoncloudwatch-agent/debian/amd64/latest/amazon-cloudwatch-agent.deb \
 && dpkg -i amazon-cloudwatch-agent.deb \
 && rm amazon-cloudwatch-agent.deb
 

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

RUN npm install
RUN npm run build
RUN cp .env.example .env || true
RUN chown -R www-data:www-data storage bootstrap/cache
RUN a2enmod rewrite

# Force Apache to write to real log files
RUN rm -f /var/log/apache2/access.log /var/log/apache2/error.log \
    && touch /var/log/apache2/access.log /var/log/apache2/error.log \
    && chmod 666 /var/log/apache2/access.log /var/log/apache2/error.log

# Disable Apache's pipe logging set by base image
RUN sed -i 's|ErrorLog ${APACHE_LOG_DIR}/error.log|ErrorLog /var/log/apache2/error.log|g' /etc/apache2/apache2.conf \
    && sed -i 's|CustomLog ${APACHE_LOG_DIR}/access.log|CustomLog /var/log/apache2/access.log|g' /etc/apache2/apache2.conf

# CW Agent config
COPY cwagent-config.json /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json

EXPOSE 80
CMD ["sh", "-c", "/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl -a fetch-config -m auto -c file:/opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json -s & php artisan config:clear && php artisan migrate --force && apachectl -D FOREGROUND"]
