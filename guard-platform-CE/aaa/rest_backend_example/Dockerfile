####
# Interstage: build the Consumer
#
# NOTES:
# Tests are skipped due to incompatibilities with OpenJDK 11 (tests should work on Java SE 8).
####
FROM maven:3-openjdk-11 AS build

COPY ./ /opt/backend
WORKDIR /opt/backend

# cache project dependencies
RUN mvn package -DskipLibertyPackage=true


FROM open-liberty:kernel

COPY --from=build --chown=1000:0  /opt/backend/target/testcase-one.war /config/apps/
COPY --from=build --chown=1000:0  /opt/backend/src/main/liberty/config/server.compiled.xml /config/server.xml
COPY --from=build --chown=1000:0  /opt/backend/keystore.xml /config/configDropins/defaults/keystore.xml
COPY --from=build --chown=1000:0  /opt/backend/src/main/liberty/config/resources/security/key.jks /config/resources/security/key.jks

RUN configure.sh

EXPOSE 19081
