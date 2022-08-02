#!/usr/bin/bash
# Author: Giovanni Grieco <giovanni.grieco@poliba.it>
####
# Convert an X.509 certificate from CER format to PEM
####

if [ -z "$1" ]; then
    echo "Please provide a CER file as input argument."
    exit 1
fi

openssl x509 -inform der -in $1 -out $(basename $1 .cer).pem
