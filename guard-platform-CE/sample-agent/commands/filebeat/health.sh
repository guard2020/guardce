#!bin/bash
# GUARD - Filebeat
# author: Alex Carrega <alessandro.carrega@cnit.it>

# Check if filebeat is running or not

TMP_PATH="/tmp"
COMPONENT="filebeat"
PROJECT="guard"
PIDFILE="$TMP_PATH/$COMPONENT.pid"
INSTALLATION_PATH="/opt/$PROJECT/$COMPONENT"

if [ -f "$PIDFILE" ]; then
    echo "$COMPONENT started"
else
    echo "$COMPONENT not started"
fi
