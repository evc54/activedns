#!/usr/bin/env bash
cd /app && composer install
wait-for-it.sh db:3306 -t 600 -- php console.php migrate --interactive=0 &
service nginx start
php-fpm
