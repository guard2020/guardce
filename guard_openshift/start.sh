oc create project guardce
oc adm policy add-scc-to-user privileged -z default -n guardce
oc create secret docker-registry docker-guard --docker-server=docker.io --docker-username=guard2020 --docker-password=Italtel2020! --docker-email=guard.project@italtel.com
oc secrets link default docker-guard --for=pull
oc import-image guard2020/idm:latest --confirm
oc import-image guard2020/zookeeper:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/kafka_confluent:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/elasticsearch:latest --confirm --scheduled=true --reference-policy='local' 
oc import-image guard2020/logstash:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/akhq:latest --confirm --scheduled=true --reference-policy='local'

oc import-image docker.io/library/busybox:latest --confirm --scheduled=true --reference-policy='local' 

oc import-image guard2020/kibana:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/cb-manager:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/smart-controller:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/mysql:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/security-dashboard:latest --confirm --scheduled=true --reference-policy='local'


oc import-image guard2020/algo1.1.2:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/algo5:latest --confirm --scheduled=true --reference-policy='local'


oc import-image guard2020/logdata-anomaly-miner:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/openvas:latest --confirm --scheduled=true --reference-policy='local'
oc import-image guard2020/kafdrop:latest --confirm --scheduled=true --reference-policy='local'

oc import-image guard2020/guard-starter:v5 --confirm --scheduled=true --reference-policy='local' 


#####  Gestione PV e PVC  ####

#### CREAZIONE SECRETS  ####

#su - user 
mkdir /home/user/secrets/idm
mkdir /home/user/idm/secrets/certs
chmod 766 /home/user/secrets/idm
cp idp.jks /home/user/secrets/idm/certs/wso2carbon.jks
oc create secret generic idm --from-file=/home/user/secrets/idm/certs
mkdir /home/user/secrets/kafka
cd /home/user/secrets/kafka
cp openshift/gen_cert.sh .
bash -x gen_cert.sh
oc create secret generic kafka-secrets --from-file=/home/user/secrets/kafka
mkdir /home/user/secrets/logstash/config
cp guard-platform/centralized_svc/logstash-guard/config/*    /home/user/secrets/logstash/config
mkdir /home/user/secrets/logstash/pipeline 
cp guard-platform/centralized_svc/logstash-guard/pipeline/*   /home/user/secrets/logstash/pipeline
oc create secret generic logstash-config --from-file=/home/user/secrets/logstash/config
oc create secret generic logstash-pipeline --from-file=/home/user/secrets/logstash/pipeline
mkdir /home/user/secrets/bl-connector
cp guard-platform/core_framework/security_services/algo4/blockchain-connector/test-blockchain-connector-data/* .
oc create secret generic blockchain-connector-data --from-file=/home/user/secrets/bl-connector
mkdir /home/user/secrets/aminer
cp  guard-platform/local_sidercars/log_data_agents/logdata-anomaly-miner/source/root/etc/aminer/template_config.yml /home/user/aminer/config.yml
oc create secret generic aminer --from-file=/home/user/secrets/aminer
mkdir /home/user/secrets/aminer-conf
cp  guard-platform/local_sidercars/log_data_agents/logdata-anomaly-miner/source/root/etc/aminer/conf-available/generic/ApacheAccessModel.py /home/user/aminer/conf-enabled/
oc create secret generic aminer-conf --from-file=/home/user/secrets/aminer/conf-enabled
mkdir /home/user/algo
cp guardce/algo* /home/user/algo
cp blockchain-connector.yaml /home/user/algo
oc create secret generic smart-controller --from-file=/home/user/algo
#####
oc apply -f idm.yaml
oc apply -f zookeeper.yaml
oc apply -f mysql.yaml
oc apply -f kafka-cluster.yaml
oc apply -f akhq.yaml
oc apply -f es-cluster.yaml
oc apply -f kibana.yaml
oc apply -f cb-manager.yaml
oc apply -f algo1.1.2.yaml 
oc apply -f smart-controller.yaml
oc apply -f logstash.yaml
oc apply -f security-dashboard.yaml
oc apply -f algo5.yaml
oc apply -f logdata-anomaly-miner.yaml
oc apply -f guard-starter.yaml

####  Generazione KAFKA-Cluster  ###
#  Per ogni broker del cluster deve essere creato un service e una route
#  La route deve chiamarsi come il DNS presente in Advertised Listener
#
#
#
#

