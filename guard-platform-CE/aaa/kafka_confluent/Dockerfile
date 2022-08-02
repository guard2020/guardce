####
# Dockerize Downloader
####
FROM alpine:latest AS dockerize-downloader

RUN apk --update add \
        wget \
    # dockerize will be used to check WSO2 is ready to accept remote connections
    && wget --progress=bar:force:noscroll -O - \
       $(wget -O - https://api.github.com/repos/powerman/dockerize/releases/latest | grep -i /dockerize-$(uname -s)-$(uname -m)\" | cut -d\" -f4) | \
       install -m0744 /dev/stdin /opt/dockerize 2>&1


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
# Runtime Container
####
FROM confluentinc/cp-kafka:6.1.0

COPY --from=dockerize-downloader --chown=appuser:appuser /opt/dockerize /etc/confluent/docker/dockerize
COPY --from=library-build /opt/lib/target/liboauthbearer-1.0.0.jar /usr/share/java/kafka/
COPY ./*.jks /etc/kafka/secrets/
COPY ./password /etc/kafka/secrets/

CMD /etc/confluent/docker/run
