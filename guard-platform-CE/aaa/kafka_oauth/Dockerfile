####
# Interstage: get Kafka from official website
#
# NOTES:
# Unfortunately, due to docker-compose design limitation, it is not possible to include
# kafka-vanilla as interstage build for kafka-oauth without running a container based on
# kafka-vanilla image. For this reason, the code here below is replicated.
####
FROM alpine:latest AS vanilla

ARG ZOOKEEPER_CONTAINER_NAME
ARG KAFKA_VER=2.6.0
ARG SCALA_VER=2.13
ENV KAFKA_DIST=${SCALA_VER}-${KAFKA_VER}

RUN apk --update add \
        wget \
    && wget --progress=bar:force:noscroll --directory-prefix /opt https://archive.apache.org/dist/kafka/${KAFKA_VER}/kafka_${KAFKA_DIST}.tgz 2>&1 \
    && tar -C /opt -xzvf /opt/kafka_${KAFKA_DIST}.tgz \
    && rm -v /opt/kafka_${KAFKA_DIST}.tgz \
    && mv -v /opt/kafka_${KAFKA_DIST} /opt/kafka \
    && sed -i "s/zookeeper.connect=.*/zookeeper.connect=${ZOOKEEPER_CONTAINER_NAME}:2181/g" /opt/kafka/config/server.properties \
    # dockerize will be used to check WSO2 is ready to accept remote connections
    && wget --progress=bar:force:noscroll -O - \
       $(wget -O - https://api.github.com/repos/powerman/dockerize/releases/latest | grep -i /dockerize-$(uname -s)-$(uname -m)\" | cut -d\" -f4) | \
       install -m0744 /dev/stdin /opt/kafka/bin/dockerize 2>&1

####
# Interstage: build the OAuth Kafka extension library
#
# NOTES:
# Tests are skipped due to incompatibilities with OpenJDK 11 (tests should work on Java SE 8).
####
FROM maven:3-openjdk-11 AS library-build

COPY ./kafka-oauth-library /opt/lib

WORKDIR /opt/lib

# cache project dependencies
RUN mvn verify clean --fail-never
RUN mvn package -DskipTests


####
# Final build: integration
####
FROM azul/zulu-openjdk-alpine:11

ENV KAFKA_OPTS=-Djava.security.auth.login.config=config/kafka_server_jaas.conf
ENV KAFKA_OAUTH_SERVER_PROP_FILE=config/broker-configuration.properties

RUN apk --update add \
        bash

COPY --from=vanilla /opt/kafka /opt/kafka
COPY --from=library-build /opt/lib/target/liboauthbearer-1.0.0.jar /opt/kafka/libs/
COPY ./config/* /opt/kafka/config/
COPY ./*.jks /var/private/ssl/

EXPOSE 9092
EXPOSE 9093

WORKDIR /opt/kafka
