#oc create project guardce
#oc adm policy add-scc-to-user privileged -z default -n guardce
#oc create secret docker-registry docker-guard --docker-server=docker.io --docker-username=guard2020 --docker-password=Italtel2020! --docker-email=guard.project@italtel.com
#oc secrets link default docker-guard --for=pull
#oc import-image guard2020/idm:latest --confirm
#oc import-image guard2020/zookeeper:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/kafka_confluent:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/elasticsearch:latest --confirm --scheduled=true --reference-policy='local' 
#oc import-image guard2020/logstash:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/akhq:latest --confirm --scheduled=true --reference-policy='local'

#oc import-image docker.io/library/busybox:latest --confirm --scheduled=true --reference-policy='local' 

#oc import-image guard2020/kibana:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/cb-manager:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/smart-controller:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/mysql:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/security-dashboard:latest --confirm --scheduled=true --reference-policy='local'

#oc import-image guard2020/algo1.2:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/algo1.1.2:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/algo5:latest --confirm --scheduled=true --reference-policy='local'

#oc import-image guard2020/guard-platform_blockchain-connector:latest --confirm --scheduled=true --reference-policy='local' 
#oc import-image guard2020/logdata-anomaly-miner:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/openvas:latest --confirm --scheduled=true --reference-policy='local'
#oc import-image guard2020/kafdrop:latest --confirm --scheduled=true --reference-policy='local'

#oc import-image guard2020/guard-starter:v5 --confirm --scheduled=true --reference-policy='local' 


#####  Gestione PV e PVC  ####

#### CREAZIONE SECRETS  ####

#su - italtel 
#mkdir /home/Italtel/secrets/idm
#mkdir /home/Italtel/idm/secrets/certs
#chmod 766 /home/Italtel/secrets/idm
#cp idp.jks (o wso2carbon.jks) /home/italtel/secrets/idm/certs/wso2carbon.jks
#oc create secret generic idm --from-file=/home/italtel/secrets/idm/certs
#mkdir /home/italtel/secrets/kafka
#cd /home/italtel/secrets/kafka
#cp openshift/gen_cert.sh .
#bash -x gen_cert.sh
#oc create secret generic kafka-secrets --from-file=/home/italtel/secrets/kafka
#mkdir /home/italtel/secrets/logstash/config
#cp guard-platform/centralized_svc/logstash-guard/config/*    /home/italtel/secrets/logstash/config
#mkdir /home/italtel/secrets/logstash/pipeline 
#cp guard-platform/centralized_svc/logstash-guard/pipeline/*   /home/italtel/secrets/logstash/pipeline
#oc create secret generic logstash-config --from-file=/home/italtel/secrets/logstash/config
#oc create secret generic logstash-pipeline --from-file=/home/italtel/secrets/logstash/pipeline
#mkdir /home/italtel/secrets/bl-connector
#cp guard-platform/core_framework/security_services/algo4/blockchain-connector/test-blockchain-connector-data/* .
#oc create secret generic blockchain-connector-data --from-file=/home/italtel/secrets/bl-connector
#mkdir /home/italtel/secrets/aminer
#cp  guard-platform/local_sidercars/log_data_agents/logdata-anomaly-miner/source/root/etc/aminer/template_config.yml /home/italtel/aminer/config.yml
#oc create secret generic aminer --from-file=/home/italtel/secrets/aminer
#mkdir /home/italtel/secrets/aminer-conf
#cp  guard-platform/local_sidercars/log_data_agents/logdata-anomaly-miner/source/root/etc/aminer/conf-available/generic/ApacheAccessModel.py /home/italtel/aminer/conf-enabled/
#oc create secret generic aminer-conf --from-file=/home/italtel/secrets/aminer/conf-enabled
#mkdir /home/italtel/algo1.2
#cp  certs /home/italtel/secrets/algo1.2
#oc create secret generic algo12 --from-file=/home/italtel/secrets/algo1.2
#mkdir /home/italtel/algo
#cp guardce/algo* /home/italtel/algo
#cp blockchain-connector.yaml /home/italtel/algo
#oc create secret generic smart-controller --from-file=/home/italtel/algo
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
oc apply -f algo1.2.yaml 
oc apply -f smart-controller.yaml
oc apply -f logstash.yaml
oc apply -f security-dashboard.yaml
oc apply -f blockchain-connector.yaml
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
