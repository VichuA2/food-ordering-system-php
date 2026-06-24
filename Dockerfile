FROM public.ecr.aws/docker/library/php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=public.ecr.aws/docker/library/composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install CloudWatch Agent
RUN curl -O https://amazoncloudwatch-agent.s3.amazonaws.com/debian/amd64/latest/amazon-cloudwatch-agent.deb \
    && dpkg -i amazon-cloudwatch-agent.deb \
    && rm amazon-cloudwatch-agent.deb

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

# CW Agent config
COPY cwagent-config.json /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json

EXPOSE 80
CMD ["sh", "-c", "/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl -a fetch-config -m ec2 -c file:/opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json -s && php artisan config:clear && php artisan migrate --force && apache2-foreground"]
