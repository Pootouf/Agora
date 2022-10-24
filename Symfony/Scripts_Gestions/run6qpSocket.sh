#!/usr/bin/env bash
echo "Lancement du WebSocket de 6 qui prend"
php ../bin/console sockets:start-sqp
echo "Extinction du WebSocket de 6 qui prend"
