#!/bin/bash
#### Usage start_config.sh internal_ip_address

if [ "$#" -ne 1 ]; then
        echo "Usage: ./start_config.sh internal_ip_address"
        exit 1
fi    

docker pull guard2020/algo1.1.2:latest
docker pull guard2020/algo5:latest
docker pull guard2020/risk-assessment:latest

curl  -X PUT http://$1:9200/*/_settings -H 'kbn-xsrf: true' -H 'Content-Type: application/json' '-d {"index" : { "number_of_replicas":0 }}'
{"acknowledged":true}

curl  -v -X POST  http://$1:5000/type/exec-env  -H 'Content-Type: application/json' -d @./cb-manager/db-ee-type.json
    
curl  -v -X POST  http://$1:5000/catalog/algorithm  -H 'Content-Type: application/json' -d @./cb-manager/cnit.json

#curl -v -X POST -H "Content-Type: application/json" http://$1:4100/self/configuration -d @./cb-manager/guard-agent.json

#curl -v -X POST -H "Content-Type: application/json" http://$1:4100/agent/type -d @./cb-manager/catalog-guard-agent.json

#curl -v -X POST -H "Content-Type: application/json" http://$1:4100/agent/instance -d @./cb-manager/instance-guard-agent.json

curl -v -X POST -H "Content-Type: application/json" http://$1:5000/type/network-link -d @./cb-manager/network-type.json

curl -v -X POST -H "Content-Type: application/json" http://$1:5000/network-link -d @./cb-manager/network-link.json

