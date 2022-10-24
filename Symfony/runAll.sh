#!/bin/bash

echo "Running all components !"
cd Scripts_Gestions
./runServer.sh &
./runWebSockets.sh
