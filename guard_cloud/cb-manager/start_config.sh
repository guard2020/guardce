#!/bin/bash
#### Usage start_config.sh internal_ip_address cb-managet-port lcp-port es-port kibana-port

if [ "$#" -ne 5 ]; then
        echo "Usage: ./start_config.sh internal_ip_address cb-managet-port lcp-port es-port kibana-port"
        exit 1
fi

docker pull guard2020/algo1.1.2:latest
docker pull guard2020/algo5:latest
docker pull guard2020/risk-assessment:latest

cd cb-manager
sed  -i -e "s/url\":X/url\": \"http:\/\/$1:$3\"/" guard-agent.json

curl -X PUT http://$1:$4/notification-index
curl -X PUT http://$1:$4/*/_settings -H 'kbn-xsrf: true' -H 'Content-Type: application/json' '-d {"index" : { "number_of_replicas":0 }}'{"acknowledged":true}

curl -v -X POST  http://$1:$2/type/exec-env  -H 'Content-Type: application/json' -d @./db-ee-type.json

curl -v -X POST  http://$1:$2/catalog/algorithm  -H 'Content-Type: application/json' -d @./cnit-ml.json

curl -v -X POST -H "Content-Type: application/json" http://$1:$3/self/configuration -d @./guard-agent.json
curl -v -X POST -H "Content-Type: application/json" http://$1:$3/agent/type -d @./catalog-guard-agent.json

curl -v -X POST -H "Content-Type: application/json" http://$1:$3/agent/instance -d @./instance-guard-agent.json

curl -v -X POST -H "Content-Type: application/json" http://$1:$2/type/network-link -d @./network-type.json

#curl -v -X POST -H "Content-Type: application/json" http://$1:$2/network-link -d @./network-link.json

#curl -v -X POST -H "Content-Type: application/json" http://$1:$2/connection -d @connection.json

curl -X POST http://$1:$5/api/saved_objects/_import -H 'kbn-xsrf: true' --form file=@aminer.ndjson
