#!/usr/bin/env bash

KAFKA_IP=$1
SSL_PORT=$2

openssl s_client -CAfile ./truststore.pem \
                 -cert ./keystore.pem     \
                 -connect $KAFKA_IP:$SSL_PORT
