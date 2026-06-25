FROM public.ecr.aws/docker/library/php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    wget \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install CloudWatch Agent
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

# Apache log files
RUN rm -f /var/log/apache2/access.log /var/log/apache2/error.log \
    && touch /var/log/apache2/access.log /var/log/apache2/error.log \
    && chmod 666 /var/log/apache2/access.log /var/log/apache2/error.log

RUN sed -i 's|ErrorLog ${APACHE_LOG_DIR}/error.log|ErrorLog /var/log/apache2/error.log|g' /etc/apache2/apache2.conf \
    && sed -i 's|CustomLog ${APACHE_LOG_DIR}/access.log|CustomLog /var/log/apache2/access.log|g' /etc/apache2/apache2.conf

# CloudWatch Agent config
COPY cwagent-config.json /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json

# Startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
