FROM ubuntu:18.04

MAINTAINER Manos Papoutsakis <paputsak@ics.forth.gr>

# install java 8
RUN apt update
RUN apt install -y openjdk-8-jdk

# install maven 3
RUN apt install -y maven && apt install -y curl jq iproute2 vim  tcpdump

# define kafkaEndpoint (IP:port)
ENV kafkaEndpoint 10.0.0.7:9092
ENV contextBrokerManagerEndpoint 10.0.0.7:5000

# define TopologyChangesTopic
ENV topologyChangesTopic TopologyChanges

# get the source code
RUN groupadd -r smartcontroller && useradd -s /bin/bash -g smartcontroller -m smartcontroller
RUN groupadd -r docker -g 998 && usermod -G docker smartcontroller
ADD . /home/smartcontroller/SmartController

ENV DOCKERVERSION=20.10.5
RUN curl -fsSLO https://download.docker.com/linux/static/stable/x86_64/docker-${DOCKERVERSION}.tgz \
    && tar xzvf docker-${DOCKERVERSION}.tgz --strip 1 -C /usr/local/bin docker/docker \
    && rm docker-${DOCKERVERSION}.tgz

# change user
RUN chown smartcontroller -R /home/smartcontroller
USER smartcontroller

RUN chmod 755 /home/smartcontroller/SmartController/algo/*.sh

# open to the world
EXPOSE 9000

# run
WORKDIR /home/smartcontroller/SmartController

RUN mvn clean package
CMD ["java", "-jar", "./target/spring-boot-kafka-app-0.0.1-SNAPSHOT.jar"]



