#! /bin/bash

composer install
(cd public ; npm install)

php-fpm -R
