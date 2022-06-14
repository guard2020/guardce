#!/bin/bash
#### Usage gen_cert.sh DNS IP_ADDRESS

keytool -genkey -keystore kafka.server.keystore.jks -validity 365 -storepass "password" -keypass "password" -dname "CN=$1" -storetype pkcs12 -keyalg RSA -keysize 4096
keytool -keystore kafka.server.keystore.jks -certreq -file kafka-cert-sign-request -storepass "password" -keypass "password" 
openssl req -new -newkey rsa:4096 -days 365 -x509 -subj "/CN=Kafka-Security-CA" -keyout ca-key -out ca-cert -nodes
openssl x509 -req -CA ca-cert -CAkey ca-key -in kafka-cert-sign-request -out kafka-cert-signed -days 365 -CAcreateserial -passin pass:"password"  -extensions SAN \
  -extfile <(cat /tmp/openssl.cnf \
    <(printf "\n[SAN]\nsubjectAltName=IP:$1,DNS:kafka1,DNS:$2")) \

keytool -keystore kafka.server.truststore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka.server.keystore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka.server.keystore.jks -import -file kafka-cert-signed -storepass "password" -keypass "password"  -noprompt
