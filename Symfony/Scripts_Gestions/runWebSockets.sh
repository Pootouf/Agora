#!/usr/bin/env bash

echo "Démarrage des WebSockets ..."

php ../bin/console sockets:start-chat &

./run6qpSocket.sh &
./runAveCesarSocket.sh &
./runSplendorSocket.sh &
./runAugustusSocket.sh &
./runMorpionSocket.sh &
./runAzulSocket.sh &
./runRRSocket.sh &
./runPuissance4Socket.sh

echo "Fermeture des WebSockets"
