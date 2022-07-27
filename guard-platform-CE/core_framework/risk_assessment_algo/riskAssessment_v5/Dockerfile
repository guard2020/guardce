FROM ubuntu:18.04

RUN apt-get update && \
      apt-get -y install sudo

# install java 8
RUN apt update 
RUN apt install -y openjdk-11-jdk

# install maven 3
RUN apt install -y maven

# define kafkaEndpoint (IP:port)
ENV kafkaEndpoint 10.0.0.7:9092
ENV KAFKA_ALGO_TOPIC vuln_scanner

# get the source code
RUN mkdir /home/riskassessment
ADD . /home/riskassessment/RiskAssessment

# change user
RUN groupadd -r riskassessment && useradd -r -g riskassessment riskassessment
RUN chown riskassessment -R /home/riskassessment
USER riskassessment

# open to the world
EXPOSE 9001

# run
WORKDIR /home/riskassessment/RiskAssessment

CMD ["sh", "./src/main/resources/initTopicToListen.sh"]

RUN mvn clean package

CMD ["java", "-jar", "./target/spring-boot-kafka-app-0.0.1-SNAPSHOT.jar"]
