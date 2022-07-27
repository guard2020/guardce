#!/bin/bash
### Usage algo1.1.2.sh command container_name kafka_topic

if [ "$1" == "start" ]; then
    if [ "$#" -ne 3 ]; then
        echo "Usage: ./Usage algo1.1.2.sh start container_name kafka_topic"
        exit 1
    fi    
    docker run -d --rm --name $2 -e KAFKA_TOPIC=$3 --net=guardce_default --env-file algo1.1.2-env guard2020/algo1.1.2:latest

    if [ $? -eq "0" ]; then    
            sleep 5
            host=`docker inspect $2 | jq '.[].NetworkSettings.Networks.guardce_default.IPAddress' | tr -d '"'`
            curl -X POST -H "Content-Type: application/json"  http://$host:9999/parameters -d '{ "kafka-bootstrap-servers": "'$kafkaEndpoint'"}'
            curl -X POST -H "Content-Type: application/json"  http://$host:9999/parameters -d "{ \"kafka-topic\": \"$3\"}"
            curl -X POST -H "Content-Type: application/json"  http://$host:9999/parameters/kafka-security-protocol/PLAINTEXT 
            curl -X POST -H "Content-Type: application/json"  http://$host:9999/parameters -d '{ "version": "v3", "report-time": 10}'
            curl -X POST -H "Content-Type: application/json"  http://$host:9999/commands/start 2>/dev/null
        exit 0
    else
        exit 1
    fi

fi

if [ "$1" == "stop" ]; then
    if [ "$#" -ne 2 ]; then
        echo "Usage: ./Usage algo1.1.2.sh stop container_name"
        exit 1
    fi
    
    docker stop $2
    if [ $? -eq "0" ]; then    
        exit 0
    else
        exit 1
    fi    
    
fi

echo "Usage: ./Usage algo1.1.2.sh start container_name kafka_topic"
exit 1
