#!/bin/sh

echo "Deployment started..."

# change to the project dir
cd ~/www/movies.nvweb.dev/public_html

# enter maintenance mode
(php84 artisan down) || true

# pull the latest from the repo
git pull origin main

# install composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# install npm packages
npm install --omit=dev

# run migrations
php84 artisan migrate --force --env=production

# clear caches
php84 artisan optimize:clear --env=production

# recreate up caches
php84 artisan optimize --env=production

# exit maintenance mode
php84 artisan up

echo "Deployment finished"
