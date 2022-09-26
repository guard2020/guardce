#!/bin/bash
### usage ./build-volumes.sh

export $(cat ../../.env | xargs)

mkdir -p $VOLUME_DIR/data/zookeeper/data
chmod 777 $VOLUME_DIR/data/zookeeper/data
mkdir -p $VOLUME_DIR/data/zookeeper/datalog
chmod 777 $VOLUME_DIR/data/zookeeper/datalog

mkdir -p $VOLUME_DIR/data/kafka1/data
chmod 777 $VOLUME_DIR/data/kafka1/data
mkdir -p $VOLUME_DIR/data/kafka2/data
chmod 777 $VOLUME_DIR/data/kafka2/data
mkdir -p $VOLUME_DIR/data/kafka3/data
chmod 777 $VOLUME_DIR/data/kafka3/data

mkdir -p $VOLUME_DIR/logstash-guard/config
chmod 777 $VOLUME_DIR/logstash-guard/config
cp ../logstash-guard/config/* $VOLUME_DIR/logstash-guard/config
mkdir -p $VOLUME_DIR/logstash-guard/pipeline
chmod 777 $VOLUME_DIR/logstash-guard/pipeline
cp ../logstash-guard/pipeline/* $VOLUME_DIR/logstash-guard/pipeline
mkdir -p $VOLUME_DIR/logstash-cb/file-output
chmod 777 $VOLUME_DIR/logstash-cb/file-output

mkdir -p $VOLUME_DIR/elastic/data01
chmod 777 $VOLUME_DIR/elastic/data01
mkdir -p $VOLUME_DIR/elastic/data02
chmod 777 $VOLUME_DIR/elastic/data02
mkdir -p $VOLUME_DIR/elastic/data03
chmod 777 $VOLUME_DIR/elastic/data03
mkdir -p $VOLUME_DIR/security_dashboard/guard_docker/run/var
chmod 777 $VOLUME_DIR/security_dashboard/guard_docker/run/var

mkdir -p $VOLUME_DIR/alert
sed  -i -e "s/hosts:X/hosts: $GUARD_SERVER_ADDRESS:$ELASTIC_PORT_1/" ../alert/config.yaml
cp ../alert/config.yaml $VOLUME_DIR/alert 


mkdir -p $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled
chmod 777 $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled
cp ../Aminer/config.yml $VOLUME_DIR/logdata-anomaly-miner/source/root/etc/aminer
cp ../Aminer/ApacheAccessModel.py v/logdata-anomaly-miner/source/root/etc/aminer/conf-enabled

mkdir -p $VOLUME_DIR/scan_reports
chmod 777 $VOLUME_DIR/scan_reports

mkdir -p $VOLUME_DIR/kafka-cluster-ssl/secrets
chmod 777 $VOLUME_DIR/kafka-cluster-ssl/secrets
cp ./gen_certs.sh $VOLUME_DIR/kafka-cluster-ssl/secrets
cd $VOLUME_DIR/kafka-cluster-ssl/secrets
chmod 755 $VOLUME_DIR/kafka-cluster-ssl/secrets/gen_certs.sh
./gen_certs.sh $GUARD_SERVER $GUARD_SERVER_ADDRESS


mkdir -p $VOLUME_DIR/certs
chmod 777 $VOLUME_DIR/certs
cp $VOLUME_DIR/kafka-cluster-ssl/secrets/idp.jks $VOLUME_DIR/certs/
exit 0

