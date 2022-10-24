#!/bin/bash

echo "Updating your schema with possibly newly created entities..."

# php bin/console doctrine:schema:update --force

php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create

echo "Update finished"
