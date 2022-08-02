#!/bin/bash
# GUARD - Filebeat
# author: Alex Carrega <alessandro.carrega@cnit.it>

# Start filebeat

TMP_PATH="/tmp"
COMPONENT="filebeat"
PROJECT="guard"
PIDFILE="$TMP_PATH/$COMPONENT.pid"
INSTALLATION_PATH="/opt/$PROJECT/$COMPONENT"

if [ -f "$PIDFILE" ] ; then
    echo "Error: $COMPONENT already started"
    echo "Note: to force the start please remove $PIDFILE"
else
    cd "$INSTALLATION_PATH"
    export $(cat .env | xargs)
    "./$COMPONENT" &
    echo "$!" > "$PIDFILE"
fi
