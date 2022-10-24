#!/bin/sh

#Cherche le pid des WebSocket et les kill

ps -ef | grep "sockets" | grep -v grep
echo "Kill all Sockets"

ps -ef | grep "sockets:start-chat" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:avecesar" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-sqp" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-splendor" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-augustus" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-morpion" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-azul" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-rr" | grep -v grep | awk '{print $2}' | xargs kill
ps -ef | grep "sockets:start-puissance4" | grep -v grep | awk '{print $2}' | xargs kill

ps -ef | grep "sockets" | grep -v grep
