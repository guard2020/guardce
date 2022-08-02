#!bin/bash
# GUARD - Filebeat
# author: Alex Carrega <alessandro.carrega@cnit.it>

# Check if filebeat is properly installed

PROJECT="guard"
COMPONENT="filebeat"
COMPONENTS="lcp $COMPONENT"
COMMANDS="start stop health"

EXIT_CODE=0
for COMPONENT in $COMPONENTS; do
    if [ -d "/opt/$PROJECT/$COMPONENT" ]; then
        echo "$COMPONENT ok"
    else
        echo "$COMPONENT not present"
        EXIT_CODE=1
    fi
done

if [ ! -d "/opt/$PROJECT/commands/$COMPONENT" ]; then
    echo "Command path not found"
    EXIT_CODE=1
else
    for COMMAND in $COMMANDS; do
        if [ -f "/opt/$PROJECT/commands/$COMPONENT/$COMMAND.sh" ]; then
            echo "$COMPONENT $COMMAND ok"
        else
            echo "$COMPONENT $COMMAND not present"
            EXIT_CODE=1
        fi
    done
fi

exit $EXIT_CODE
