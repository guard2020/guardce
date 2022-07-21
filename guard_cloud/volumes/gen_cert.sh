#!/bin/bash
#### Usage gen_cert.sh DNS IP_ADDRESS

keytool -genkey -keystore kafka1.server.keystore.jks -validity 365 -storepass "password" -keypass "password" -dname "CN=$1" -storetype pkcs12 -keyalg RSA -keysize 4096
keytool -genkey -keystore kafka2.server.keystore.jks -validity 365 -storepass "password" -keypass "password" -dname "CN=$1" -storetype pkcs12 -keyalg RSA -keysize 4096
keytool -genkey -keystore kafka3.server.keystore.jks -validity 365 -storepass "password" -keypass "password" -dname "CN=$1" -storetype pkcs12 -keyalg RSA -keysize 4096

keytool -keystore kafka1.server.keystore.jks -certreq -file kafka1-cert-sign-request -storepass "password" -keypass "password" 
keytool -keystore kafka2.server.keystore.jks -certreq -file kafka2-cert-sign-request -storepass "password" -keypass "password" 
keytool -keystore kafka3.server.keystore.jks -certreq -file kafka3-cert-sign-request -storepass "password" -keypass "password" 
openssl req -new -newkey rsa:4096 -days 365 -x509 -subj "/CN=Kafka-Security-CA" -keyout ca-key -out ca-cert -nodes
openssl x509 -req -CA ca-cert -CAkey ca-key -in kafka1-cert-sign-request -out kafka1-cert-signed -days 365 -CAcreateserial -passin pass:"password"  -extensions SAN \
  -extfile <(cat /tmp/openssl.cnf \
    <(printf "\n[SAN]\nsubjectAltName=DNS:$1,DNS:kafka1,DNS:kafka2,DNS:kafka3,IP:$2")) 
openssl x509 -req -CA ca-cert -CAkey ca-key -in kafka2-cert-sign-request -out kafka2-cert-signed -days 365 -CAcreateserial -passin pass:"password"  -extensions SAN \
  -extfile <(cat /tmp/openssl.cnf \
    <(printf "\n[SAN]\nsubjectAltName=DNS:$1,DNS:kafka1,DNS:kafka2,DNS:kafka3,IP:$2")) 
openssl x509 -req -CA ca-cert -CAkey ca-key -in kafka3-cert-sign-request -out kafka3-cert-signed -days 365 -CAcreateserial -passin pass:"password"  -extensions SAN \
  -extfile <(cat /tmp/openssl.cnf \
    <(printf "\n[SAN]\nsubjectAltName=DNS:$1,DNS:kafka1,DNS:kafka2,DNS:kafka3,IP:$2")) 



keytool -keystore kafka.server.truststore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka1.server.keystore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka2.server.keystore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka3.server.keystore.jks -alias CARoot -import -file ca-cert -storepass "password" -keypass "password" -noprompt
keytool -keystore kafka1.server.keystore.jks -import -file kafka1-cert-signed -storepass "password" -keypass "password"  -noprompt
keytool -keystore kafka2.server.keystore.jks -import -file kafka2-cert-signed -storepass "password" -keypass "password"  -noprompt
keytool -keystore kafka3.server.keystore.jks -import -file kafka3-cert-signed -storepass "password" -keypass "password"  -noprompt


### IDP ###
keytool -genkey -alias wso2carbon -keystore idp.jks -validity 365 -storepass "wso2carbon" -keypass "wso2carbon" -dname "CN=wso2carbon" -storetype jks -keyalg RSA -keysize 4096

keytool -keystore idp.jks -alias wso2carbon -certreq -file idp-cert-sign-request -storepass "wso2carbon" -keypass "wso2carbon" 

cp /etc/ssl/openssl.cnf /tmp

openssl x509 -req -CA ca-cert -CAkey ca-key -in idp-cert-sign-request -out idp-cert-signed -days 365 -CAcreateserial -passin pass:"keytool -keystore idp.jks -certreq -file idp-cert-sign-request -storepass "wso2carbon" -keypass "wso2carbon" 
"  -extensions SAN \
  -extfile <(cat /tmp/openssl.cnf \
    <(printf "\n[SAN]\nsubjectAltName=DNS:$1,DNS:idp,DNS:wso2carbon,IP:$2\nextendedKeyUsage=serverAuth,clientAuth")) 

keytool -keystore idp.jks -alias CARoot -import -file ca-cert -storepass "wso2carbon" -keypass "wso2carbon" -noprompt
keytool -keystore idp.jks -alias wso2carbon -import -file idp-cert-signed -storepass "wso2carbon" -keypass "wso2carbon"  -noprompt

