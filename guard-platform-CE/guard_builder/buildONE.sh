#!/bin/bash
#1 git clone guard_platform
#### First: docker login for repository_name
#### Usage: ./buildONE.sh repository_name [component] ####
####

if [ "$#" -lt 1 ] || [ "$#" -gt 2 ] ; then
    echo "Usage: ./buildONE.sh repository_name [component]"
    exit 0
fi

docker-compose -f docker-compose-builder.yml  build  $2

docker pull guardproject/cb-manager:master
docker tag guardproject/cb-manager:master cb-manager:master
docker rmi guardproject/cb-manager:master
docker pull guardproject/cnit_ml:version3
docker tag guardproject/cnit_ml:version3 algo1.1.2:latest
docker rmi guardproject/cnit_ml:version3
docker pull k8s.gcr.io/kubernetes-zookeeper:1.0-3.4.10
docker pull obsidiandynamics/kafdrop:latest
docker tag obsidiandynamics/kafdrop:latest kafdrop:latest
docker rmi obsidiandynamics/kafdrop:latest
docker pull docker.elastic.co/logstash/logstash:7.11.0
docker tag docker.elastic.co/logstash/logstash:7.11.0  logstash:7.11.0
docker rmi docker.elastic.co/logstash/logstash:7.11.0
docker pull docker.elastic.co/elasticsearch/elasticsearch:7.10.1
docker tag docker.elastic.co/elasticsearch/elasticsearch:7.10.1 elasticsearch:7.10.1
docker rmi docker.elastic.co/elasticsearch/elasticsearch:7.10.1
docker pull docker.elastic.co/kibana/kibana:7.10.1
docker tag docker.elastic.co/kibana/kibana:7.10.1 kibana:7.10.1
docker rmi docker.elastic.co/kibana/kibana:7.10.1
docker pull mysql:5.7
docker pull mikesplain/openvas:latest
docker tag mikesplain/openvas:latest openvas:latest
docker rmi mikesplain/openvas:latest
bash ./guard_builder/tag-version $1
exit 0

