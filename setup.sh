#!/bin/bash

composer install --optimize-autoloader
composer dump-autoload
php bin/console make:migration
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console assets:install
php bin/console doctrine:fixtures:load --no-interaction
php bin/console cache:clear