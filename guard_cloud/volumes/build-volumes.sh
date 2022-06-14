#!/bin/bash
### usage ./build-volumes-min.sh VOLUME_DIR DNS_SERVER IP_SERVER

if [ "$#" -ne 3 ]; then
        echo "Usage: ./Usage build-volumes.sh VOLUME_DIR DNS_SERVER IP_SERVER"
        exit 1
    fi    

mkdir -p $1/data/zookeeper/data
mkdir -p $1/data/zookeeper/datalog

mkdir -p $1/data/kafka1/data
mkdir -p $1/data/kafka2/data
mkdir -p $1/data/kafka3/data

mkdir -p $1/logstash-guard/config
cp ../logstash-guard/config/* $1/logstash-guard/config
mkdir -p $1/logstash-guard/pipeline
cp ../logstash-guard/pipeline/* $1/logstash-guard/pipeline
mkdir -p $1/logstash-cb/file-output

mkdir -p $1/elastic/data01
mkdir -p $1/elastic/data02
mkdir -p $1/elastic/data03
mkdir -p $1/security_dashboard/guard_docker/run/var

mkdir -p $1/alert
cp ../alert/config.yaml $1/alert 

mkdir -p $1/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled
cp ../Aminer/config.yml $1/logdata-anomaly-miner/source/root/etc/aminer
cp ../Aminer/ApacheAccessModel.py $1/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled

mkdir -p $1/scan_reports

mkdir -p $1/kafka-cluster-ssl/secrets
cp ./gen_certs.sh $1/kafka-cluster-ssl/secrets
cd $1/kafka-cluster-ssl/secrets
chmod 755 $1/kafka-cluster-ssl/secrets/gen_certs.sh
./gen_certs.sh $2 $3


exit 0
