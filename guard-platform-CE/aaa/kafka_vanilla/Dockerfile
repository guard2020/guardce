FROM alpine:latest AS builder

ARG ZOOKEEPER_CONTAINER_NAME
ARG KAFKA_VER=2.6.2
ARG SCALA_VER=2.13
ENV KAFKA_DIST=${SCALA_VER}-${KAFKA_VER}

RUN apk --update add \
        wget \
    && wget --progress=bar:force:noscroll --directory-prefix /opt https://downloads.apache.org/kafka/${KAFKA_VER}/kafka_${KAFKA_DIST}.tgz 2>&1 \
    && tar xf /opt/kafka_${KAFKA_DIST}.tgz -C /opt/ \
    && rm /opt/kafka_${KAFKA_DIST}.tgz \
    && mv /opt/kafka_${KAFKA_DIST} /opt/kafka \
    && sed -i "s/zookeeper.connect=.*/zookeeper.connect=${ZOOKEEPER_CONTAINER_NAME}:2181/g" /opt/kafka/config/server.properties

FROM azul/zulu-openjdk-alpine:11

RUN apk --update add \
        bash

COPY --from=builder /opt/kafka /opt/kafka

WORKDIR /opt/kafka
