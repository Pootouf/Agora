#!/bin/bash
./Scripts_Gestions/updateSchema.sh
php bin/console doctrine:database:import Data/Game.sql
echo "Fixed!"