#!/bin/bash
### Usage risk-assessment.sh command container_name kafka_topic


if [ "$1" == "start" ]; then
    if [ "$#" -ne 3 ]; then
        echo "Usage: ./Usage assessment.sh start container_name kafka_topic"
        exit 1
    fi    
    docker run -d --rm --name $2 -e KAFKA_ALGO_TOPIC=$3 --env-file risk-assessment-env --net=italtel_default guard2020/risk-assessment:latest
    if [ $? -eq "0" ]; then    
        exit 0
    else
        exit 1
    fi

fi

if [ "$1" == "stop" ]; then
    if [ "$#" -ne 2 ]; then
        echo "Usage: ./Usage risk-assessment.sh stop container_name"
        exit 1
    fi
    
    docker stop $2
    if [ $? -eq "0" ]; then    
        exit 0
    else
        exit 1
    fi    
    
fi

echo "Usage: ./Usage risk-assessment.sh start container_name kafka_topic"
exit 1
