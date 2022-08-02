####
# Interstage: build the Producer
#
# NOTES:
# Tests are skipped due to incompatibilities with OpenJDK 11 (tests should work on Java SE 8).
####
FROM maven:3-openjdk-11 AS build

COPY ./ /opt/producer
# Copy necessary dependencies, like the Kafka OAuth Library
COPY --from=guard/aaa/confluent_kafka:latest /usr/share/java/kafka/liboauthbearer-1.0.0.jar /opt/deps/

WORKDIR /opt/producer

# cache project dependencies
RUN mvn install:install-file \
        -Dfile=/opt/deps/liboauthbearer-1.0.0.jar \
        -DgroupId=org.telematics.guard.aa.kafka \
        -DartifactId=liboauthbearer \
        -Dversion=1.0.0 \
        -Dpackaging=jar \
        && \
    mvn verify clean --fail-never && \
    mvn package -DskipTests

####
# Final build: integration
####
FROM azul/zulu-openjdk-alpine:11

ENV KAFKA_OAUTH_SERVER_PROP_FILE=oauth-configuration.properties

RUN apk --update add \
        bash \
    # dockerize will be used to check WSO2 is ready to accept remote connections
    && wget -q -O - $(wget -O - https://api.github.com/repos/powerman/dockerize/releases/latest | grep -i /dockerize-$(uname -s)-$(uname -m)\" | cut -d\" -f4) | install -m0744 /dev/stdin /opt/dockerize 2>&1

COPY --from=build \
    /opt/producer/target/kafka-producer-example-1.0.0-SNAPSHOT-jar-with-dependencies.jar \
    /opt/producer.jar
COPY --from=build \
    /opt/producer/src/main/resources/oauth-configuration.properties \
    /opt/oauth-configuration.properties
COPY ./*.jks /var/private/ssl/
WORKDIR /opt
