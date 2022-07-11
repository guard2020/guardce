#!/bin/bash
### usage ./build-volumes-min.sh 

export $(cat ../.env | xargs)

mkdir -p $VOLUME_DIR/data/zookeeper/data
chmod 777 $VOLUME_DIR/data/zookeeper/data
mkdir -p $VOLUME_DIR/data/zookeeper/datalog
chmod 777 $VOLUME_DIR/data/zookeeper/datalog
mkdir -p $VOLUME_DIR/data/kafka1/data
chmod 777 $VOLUME_DIR/data/kafka1/data

mkdir -p $VOLUME_DIR/logstash-guard/config
cp ../logstash-guard/config/* $VOLUME_DIR/logstash-guard/config
mkdir -p $VOLUME_DIR/logstash-guard/pipeline
cp ../logstash-guard/pipeline/* $VOLUME_DIR/logstash-guard/pipeline
mkdir -p $VOLUME_DIR/logstash-cb/file-output
chmod 777 $VOLUME_DIR/logstash-cb/file-output

mkdir -p $VOLUME_DIR/elastic/data01
chmod 777 $VOLUME_DIR/elastic/data01

mkdir -p $VOLUME_DIR/alert
sed  -i -e "s/hosts:X/hosts: $GUARD_SERVER_ADDRESS:$ELASTIC_PORT_1/" ../alert/config.yaml
cp ../alert/config.yaml $VOLUME_DIR/alert 

mkdir -p $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled
cp ../Aminer/config.yml $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer
cp ../Aminer/ApacheAccessModel.py $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled

mkdir -p $VOLUME_DIR/scan_reports
chmod 777 $VOLUME_DIR/scan_reports

chmod 666 /var/run/docker.sock
exit 0
