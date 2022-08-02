#!/bin/bash
### Usage algo1.2 command container_name kafka_topic


if [ "$1" == "start" ]; then
    if [ "$#" -ne 3 ]; then
        echo "Usage: ./Usage algo1.2.sh start container_name kafka_topic"
        exit 1
    fi    
    docker run -d --rm --name $2 -e KAFKA_ALGO_TOPIC=$3 --net=italtel_default --env-file algo1.2-env -v italtel_algo12-certs:/app/ssl/certs guard2020/algo1.2:latest
    if [ $? -eq "0" ]; then    
      exit 0
    else
      exit 1
    fi
fi

if [ "$1" == "stop" ]; then
    if [ "$#" -ne 2 ]; then
        echo "Usage: ./Usage algo1.2.sh stop container_name"
        exit 0
    fi
    
    docker stop $2
    
    if [ $? -eq "0" ]; then   
      exit 0
    else
      exit 1
    fi
fi
echo "Usage: ./Usage algo1.2.sh start container_name kafka_topic"
exit 1