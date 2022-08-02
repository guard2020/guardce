#!/bin/bash

set -x # trace
#set -e # stop on first error

####
# Check if boostrap already run
####
stat .configured &> /dev/null
if [ "$?" -ne "1" ]; then
    ./init.sh
    exit 0
fi

####
# WSO2 Constants
####
export WSO2_USERNAME=admin
export WSO2_PASSWORD=admin
export WSO2_IS_DEFAULT_PORT=9443

####
# Utility library
####
source /bootstrap/lib.sh

####
# Setup IdP X.509 Certificate
####
# sed -i 's/<KeyAlias>wso2carbon<\/KeyAlias>/<KeyAlias>idp<\/KeyAlias>/g' \
#     wso2is-5.7.0/repository/conf/carbon.xml
# sed -i -e 's/"SAML.IdPCertAlias" : "wso2carbon"/"SAML.IdPCertAlias" : "idp"/g' \
#     -e 's/"SAML.PrivateKeyAlias": "wso2carbon"/"SAML.PrivateKeyAlias": "idp"/g' \
#     wso2is-5.7.0/repository/deployment/server/jaggeryapps/dashboard/authentication/auth_config.json
# sed -i -e 's/SAML.IdPCertAlias=wso2carbon/SAML.IdPCertAlias=idp/g' \
#     -e 's/SAML.PrivateKeyAlias=wso2carbon/SAML.PrivateKeyAlias=idp/g' \
#     wso2is-5.7.0/repository/components/features/org.wso2.carbon.webapp.mgt.server_4.7.19/conf/security/sso-sp-config.properties


####
# Start WSO2 in a local port, so other containers do not interfere during bootstrap
####
export WSO2_IS_TEMP_PORT=9442
export WSO2_IS_FINAL_PORT=10443
sed -i "s/${WSO2_IS_DEFAULT_PORT}/${WSO2_IS_TEMP_PORT}/g" /home/wso2carbon/wso2is-5.7.0/repository/conf/tomcat/catalina-server.xml

####
# Start bootstrap of WSO2 Carbon
####
echo "WSO2 Carbon is bootstrapping. This process could take a while."  # Notify Docker user
/home/wso2carbon/init.sh > init.out &
watch -g "grep 'WSO2 Carbon started in'" /home/wso2carbon/init.out

#generate_wso2_certificate - DISABLED, WE DO NOT HAVE CERTS
# cat certs/*.pem > .bootstrap/wso2.pem
# cp certs/wso2carbon.jks .bootstrap/wso2carbon.jks
# docker cp .bootstrap/wso2carbon.jks ${DOCKER_CONTAINER_NAME}:/home/wso2carbon/wso2is-5.10.0/repository/resources/security/


####
# Register Kafka as OAuth2 Service Provider
####
export SP_NAME=kafka-broker
export SP_OAUTH2_KEY=kafka-broker
export SP_OAUTH2_SECRET=yGC8KxKYYQ_q1UbTBAwxivThaZIa

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register Kafka Consumer Example as OAuth2 Service Provider
####
export SP_NAME=kafka-consumer-example
export SP_OAUTH2_KEY=kafka-consumer-example
export SP_OAUTH2_SECRET=aCpTfjcuVP9T3BgLD3s8LfXXOoMa

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register Kafka Producer Example as OAuth2 Service Provider
####
export SP_NAME=kafka-producer-example
export SP_OAUTH2_KEY=kafka-producer-example
export SP_OAUTH2_SECRET=iV8vTaWP_OH4pxBGoNVOV4rZOQ4a

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register Kafka Spring Client as OAuth2 Service Provider
####
export SP_NAME=kafka-spring-client-oauth
export SP_OAUTH2_KEY=kafka-spring-client-oauth
export SP_OAUTH2_SECRET=iV8vTaWP_OH4pxBGoNVOV4rZOQ4b

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register Kafka Logstash Example as OAuth2 Service Provider
####
export SP_NAME=kafka-logstash-example
export SP_OAUTH2_KEY=kafka-logstash-example
export SP_OAUTH2_SECRET=abvr2cGgTqKBSYFTM4gzsG7tmTY4

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register REST Backend Container Configuration
####
export SP_NAME=rest-backend-example
export SP_OAUTH2_KEY=xYaTz_5XfphvBLBbcpRWoQC8VOca
export SP_OAUTH2_SECRET=yGC8KxKYYQ_q1UbTBAwxivThaZIa

register_oauth_service_provider_with_callback $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET


####
# Register REST Frontend Container Configuration
####
export SP_NAME=rest-frontend-example
export SP_OAUTH2_KEY=xYaTz_5XfphvBLBbcpRWoQC8VOcb
export SP_OAUTH2_SECRET=yGC8KxKYYQ_q1UbTBAwxivThaZIb

register_oauth_service_provider_with_callback $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET 'http://localhost:15000/callback'


####
# Create Google OAuth2 IdP Link
####
export IDP_NAME=GoogleOauthUc2
export GOOGLE_CLIENT_ID=294866802175-3opqol0g8v3grk3uej3a7tqa0dtrobol.apps.googleusercontent.com
export GOOGLE_CLIENT_SECRET=XsWyeY-wwFV7bYXqKM_mFpFg
export GOOGLE_OAUTH_SCOPES="scope=email"

create_google_oauth2_identity_provider $IDP_NAME $WSO2_USERNAME $WSO2_PASSWORD $IDP_HOST $GOOGLE_CLIENT_ID $GOOGLE_CLIENT_SECRET $GOOGLE_OAUTH_SCOPES


####
# Register Google OAuth2 REST Service Example
####
export SP_NAME=service-google-oauth
export SP_OAUTH2_KEY=xns9bF0Si4bMyOwica50ZYFphnAa
export SP_OAUTH2_SECRET=9mkfvantMSv8F4UugLdnXbfjfaAa

register_google_oauth_service_provider_with_callback $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET "http://${IDP_HOST}/callback" $IDP_NAME

####
# Register algo1.2 as Service Provider - Instance ID 1
####
export SP_NAME=service-algo1.2_instance_1
export SP_OAUTH2_KEY=XXOSSeamgNef3945snnxrAxKIt0a
export SP_OAUTH2_SECRET=5CsFBAp80bZamkEfeR5hJd7_HZca

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Register algo1.2 as Service Provider - Instance ID 2
####
export SP_NAME=service-algo1.2_instance_2
export SP_OAUTH2_KEY=XXOSSeamgNef3945snnxrAxKIt0b
export SP_OAUTH2_SECRET=5CsFBAp80bZamkEfeR5hJd7_HZcb

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Register algo1.2 as Service Provider - Instance ID 3
####
export SP_NAME=service-algo1.2_instance_3
export SP_OAUTH2_KEY=XXOSSeamgNef3945snnxrAxKIt0c
export SP_OAUTH2_SECRET=5CsFBAp80bZamkEfeR5hJd7_HZcc

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Register CB-Manager as Service Provider
####
export SP_NAME=cb-manager
export SP_OAUTH2_KEY=nQ7jZvd1HDQmMe1BFpzuSHoWF1sa
export SP_OAUTH2_SECRET=ceriM7nMyG_Lev5Io0W5cBu0VsYa

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Register Womcom API as Service Provider
####
export SP_NAME=wobcom-api
export SP_OAUTH2_KEY=2IBCAP_TdAy7ofvQtW7z7AlKZjIa
export SP_OAUTH2_SECRET=VSwnKCcrQQUfTgWf5v5jqR30Ih0a

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Register FORTH security service as Service Provider
####
export SP_NAME=forth-security-service
export SP_OAUTH2_KEY=YDrHfCSMnljovYNXRDSAk1gG1W8a
export SP_OAUTH2_SECRET=vtw_UqpTPSeHLWP1PV1JinfXhIIa

register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET

####
# Do not re-run this script again anymore.
####
touch .configured

####
# Restart WSO2 in foreground
####
WSO2_PID=$(ps -x | grep java | awk '{ print $1; }')
kill $WSO2_PID
# Restore WSO2 port to access administration interface
sed -i "s/${WSO2_IS_TEMP_PORT}/${WSO2_IS_FINAL_PORT}/g" /home/wso2carbon/wso2is-5.7.0/repository/conf/tomcat/catalina-server.xml

/home/wso2carbon/init.sh
