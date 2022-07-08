#!/bin/bash
### usage ./build-volumes-min.sh VOLUME_DIR  

if [ "$#" -ne 1 ]; then
        echo "Usage: ./Usage build-volumes-min.sh VOLUME_DIR "
        exit 1
    fi    

mkdir -p $1/data/zookeeper/data
chmod 777 $1/data/zookeeper/data
mkdir -p $1/data/zookeeper/datalog
chmod 777 $1/data/zookeeper/datalog
mkdir -p $1/data/kafka1/data
chmod 777 $1/data/kafka1/data

mkdir -p $1/logstash-guard/config
cp ../logstash-guard/config/* $1/logstash-guard/config
mkdir -p $1/logstash-guard/pipeline
cp ../logstash-guard/pipeline/* $1/logstash-guard/pipeline
mkdir -p $1/logstash-cb/file-output
chmod 777 $1/logstash-cb/file-output

mkdir -p $1/elastic/data01
chmod 777 $1/elastic/data01

mkdir -p $1/alert
cp ../alert/config.yaml $1/alert 

mkdir -p $1/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled
cp ../Aminer/config.yml $1/logdata-anomaly-miner/source/root/etc/aminer
cp ../Aminer/ApacheAccessModel.py $1/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled

mkdir -p $1/scan_reports
chmod 777 $1/scan_reports

chmod 666 /var/run/docker.sock
exit 0
