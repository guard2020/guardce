#!/bin/bash
# GUARD - CB Manager
# author: Alex Carrega <alessandro.carrega@cnit.it>

set_var() {
    [ ! -v $1 ] && export $1="$2"
}

set_var COMPONENT cb-manager
set_var VERSION master
set_var PROJECT guard

set_var INSTALLATION_PATH "/opt/guard/$COMPONENT"
set_var COMMANDS_PATH "/opt/guard/commands"
set_var TMP_PATH /tmp

set_var PIDFILE "$TMP_PATH/$COMPONENT.pid"

WORK_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source "$WORK_PATH/../settings/$VERSION/.env"
export $(cut -d= -f1 "$WORK_PATH/../settings/$VERSION/.env")
