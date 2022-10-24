php bin/console assets:install
php bin/console cache:clear -e prod
php bin/console cache:warmup -e prod
