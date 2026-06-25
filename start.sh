#!/bin/bash
set -e

echo "Starting CloudWatch Agent..."

# Start CloudWatch Agent
/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent \
-config /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json &

echo "Preparing Laravel..."

mkdir -p /var/www/html/storage/logs
touch /var/www/html/storage/logs/laravel.log
chmod 666 /var/www/html/storage/logs/laravel.log

php artisan config:clear
php artisan migrate --force

echo "Starting Apache..."

exec apachectl -D FOREGROUND
