####
# Interstage: build the Producer
#
# NOTES:
# Tests are skipped due to incompatibilities with OpenJDK 11 (tests should work on Java SE 8).
####
FROM maven:3-openjdk-11 AS build

# Copy necessary dependencies, like the Kafka OAuth Library
COPY --from=guard/aaa/confluent_kafka:latest /usr/share/java/kafka/liboauthbearer-1.0.0.jar /opt/deps/

WORKDIR /opt/producer

# cache project dependencies
RUN mvn install:install-file \
        -Dfile=/opt/deps/liboauthbearer-1.0.0.jar \
        -DgroupId=org.telematics.guard.aa.kafka \
        -DartifactId=liboauthbearer \
        -Dversion=1.0.0 \
        -Dpackaging=jar

COPY ./pom.xml /opt/producer/
RUN mvn dependency:go-offline

COPY ./ /opt/producer
RUN mvn clean package -DskipTests


####
# Final build: integration
####
FROM azul/zulu-openjdk-alpine:11

RUN apk --update add \
        bash

COPY --from=build \
    /opt/producer/target/spring-boot-kafka-app-1.0.0-SNAPSHOT.jar \
    /opt/producer.jar
COPY ./*.jks /var/private/ssl/
WORKDIR /opt
