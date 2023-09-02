#!/bin/bash
set -e

php artisan passport:keys
php artisan migrate

# Start Apache web server
apache2-foreground
