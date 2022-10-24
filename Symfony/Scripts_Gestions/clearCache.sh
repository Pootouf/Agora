#!/bin/bash

echo "Clearing cache"
php bin/console cache:clear
php bin/console assets:install
echo "Nicely done ! ;)"

