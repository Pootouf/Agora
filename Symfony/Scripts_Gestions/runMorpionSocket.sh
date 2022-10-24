#!/usr/bin/env bash
echo "Lancement du WebSocket de Morpion"
php ../bin/console sockets:start-morpion
echo "Extinction du WebSocket de Morpion"
