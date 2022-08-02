#!/bin/bash
### Usage algo5.sh command container_name kafka_topic



if [ "$1" == "start" ]; then
    if [ "$#" -ne 3 ]; then
        echo "Usage: ./Usage algo5.sh start container_name kafka_topic"
        exit 0
    fi    
    docker run -d --rm --name $2 -e KAFKA_TOPIC=$2 --net=italtel_algo5_net -v italtel_algo5-scan_reports:/opt/algo5/Vulnerability/reports guard2020/algo5:latest

    
    if [ $? -eq "0" ]; then   
        exit 0
    else
        exit 1
    fi
fi

if [ "$1" == "stop" ]; then
    if [ "$#" -ne 2 ]; then
        echo "Usage: ./Usage algo5.sh stop container_name"
        exit 0
    fi
    
    docker stop $2
    
    if [ $? -eq "0" ]; then  
        exit 0
    else
        exit 1
    fi
fi


 echo "Usage: ./Usage algo5.sh start container_name kafka_topic"
 exit 1