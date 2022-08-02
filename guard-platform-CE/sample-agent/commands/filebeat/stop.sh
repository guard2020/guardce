#!/bin/bash
# GUARD - Filebeat
# author: Alex Carrega <alessandro.carrega@cnit.it>

# Stop filebeat

TMP_PATH="/tmp"
COMPONENT="filebeat"
PROJECT="guard"
PIDFILE="$TMP_PATH/$COMPONENT.pid"
INSTALLATION_PATH="/opt/$PROJECT/$COMPONENT"

if [ -f "$PIDFILE" ]; then
    kill -9 $(cat "$PIDFILE")
    rm -f "$PIDFILE"
else
    echo "Error: $COMPONENT not started"
fi
