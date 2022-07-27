#Exit if any error occurs
set -e


#Import config
source config.sh

#Import helping functions
source functions.sh

#Print executing command
set -x

#Download fabric binaries
download_fabric_binaries


rm -rf data/*
mkdir -p data

#Clean


docker rm -f orderers-tlsca 2> /dev/null || true
docker rm -f peers-tlsca 2> /dev/null || true
docker rm -f orderers-rca 2> /dev/null || true
docker rm -f peers-rca 2> /dev/null || true

docker rm -f orderer1 2> /dev/null || true
docker rm -f orderer2 2> /dev/null || true
docker rm -f orderer3 2> /dev/null || true

docker rm -f peer1 2> /dev/null || true
docker rm -f peer2 2> /dev/null || true
docker rm -f peer3 2> /dev/null || true


docker rm -f chaincode-storage 2> /dev/null || true
docker rm -f blockchain-connector 2> /dev/null || true


docker rmi -f chaincode/storage-chaincode:1.0 2> /dev/null || true
docker rmi -f chaincode/blockchain-connector:1.0 2> /dev/null || true



rm -rf data/certs/
rm -rf data/crypto-config/
rm -rf data/channel-artifacts/

rm -rf data/orderers-tlsca-data/
rm -rf data/peers-tlsca-data/
rm -rf data/orderers-rca-data/
rm -rf data/peers-rca-data/


mkdir data/certs
mkdir data/crypto-config/
mkdir data/channel-artifacts/

mkdir data/orderers-tlsca-data
mkdir data/peers-tlsca-data
mkdir data/orderers-rca-data
mkdir data/peers-rca-data

cp config/orderers-rca-server-config.yaml data/orderers-rca-data/fabric-ca-server-config.yaml
cp config/orderers-tlsca-server-config.yaml data/orderers-tlsca-data/fabric-ca-server-config.yaml
cp config/peers-rca-server-config.yaml data/peers-rca-data/fabric-ca-server-config.yaml
cp config/peers-tlsca-server-config.yaml data/peers-tlsca-data/fabric-ca-server-config.yaml


docker run -d -p 7150:7150 \
--name orderers-tlsca \
-e FABRIC_CA_HOME='/var/hyperledger/production' \
-v ${PWD}/data/orderers-tlsca-data:/var/hyperledger/production \
hyperledger/fabric-ca:amd64-1.4.9 sh -c 'fabric-ca-server start'

docker run -d -p 7151:7151 \
--name peers-tlsca \
-e FABRIC_CA_HOME='/var/hyperledger/production' \
-v ${PWD}/data/peers-tlsca-data:/var/hyperledger/production \
hyperledger/fabric-ca:amd64-1.4.9 sh -c 'fabric-ca-server start'

docker run -d -p 7152:7152 \
--name orderers-rca \
-e FABRIC_CA_HOME='/var/hyperledger/production' \
-v ${PWD}/data/orderers-rca-data:/var/hyperledger/production \
hyperledger/fabric-ca:amd64-1.4.9 sh -c 'fabric-ca-server start'

docker run -d -p 7153:7153 \
--name peers-rca \
-e FABRIC_CA_HOME='/var/hyperledger/production' \
-v ${PWD}/data/peers-rca-data:/var/hyperledger/production \
hyperledger/fabric-ca:amd64-1.4.9 sh -c 'fabric-ca-server start'

#Wait for CA's to start
set +x 
wait_for_logs_occurs orderers-tlsca "[INFO] Listening on"
wait_for_logs_occurs peers-tlsca "[INFO] Listening on"
wait_for_logs_occurs orderers-rca "[INFO] Listening on"
wait_for_logs_occurs peers-rca "[INFO] Listening on"
set -x

#Download CA's cert files
curl -k https://localhost:7150/cainfo | jq -r .result.CAChain | base64 -d - > data/certs/orderers-tlsca-cert.pem
curl -k https://localhost:7151/cainfo | jq -r .result.CAChain | base64 -d - > data/certs/peers-tlsca-cert.pem
curl -k https://localhost:7152/cainfo | jq -r .result.CAChain | base64 -d - > data/certs/orderers-rca-cert.pem
curl -k https://localhost:7153/cainfo | jq -r .result.CAChain | base64 -d - > data/certs/peers-rca-cert.pem

#export FABRIC_CA_CLIENT_LOGLEVEL=warning

#Register orderers identities with the orderers-tlsca
bin/fabric-ca-client enroll -u https://$ORDERERS_TLSCA_SERVER_USER:$ORDERERS_TLSCA_SERVER_PASS@localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem 
bin/fabric-ca-client register --id.name $ORDERER1_USER --id.secret $ORDERER1_TLSCA_PASS --id.type orderer -u https://localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $ORDERER2_USER --id.secret $ORDERER2_TLSCA_PASS --id.type orderer -u https://localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $ORDERER3_USER --id.secret $ORDERER3_TLSCA_PASS --id.type orderer -u https://localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $ORDERERS_ADMIN_USER --id.secret $ORDERERS_ADMIN_TLSCA_PASS --id.type admin -u https://localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem

#Generate orderers local TLS
bin/fabric-ca-client enroll -u https://$ORDERER1_USER:$ORDERER1_TLSCA_PASS@localhost:7150 --enrollment.profile tls -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/tls --csr.hosts orderer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERER2_USER:$ORDERER2_TLSCA_PASS@localhost:7150 --enrollment.profile tls -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/tls --csr.hosts orderer2,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERER3_USER:$ORDERER3_TLSCA_PASS@localhost:7150 --enrollment.profile tls -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/tls --csr.hosts orderer3,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERERS_ADMIN_USER:$ORDERERS_ADMIN_TLSCA_PASS@localhost:7150 --enrollment.profile tls -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/tls

#Rename TLS keystore
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/tls/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/tls/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/tls/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/tls/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/tls/keystore/priv_sk


mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/tls/tlscacerts/orderers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/tls/tlscacerts/orderers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/tls/tlscacerts/orderers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/tls/tlscacerts/orderers-tlsca-cert.pem

#Register peers identities with the peers-tlsca
bin/fabric-ca-client enroll -u https://$PEERS_TLSCA_SERVER_USER:$PEERS_TLSCA_SERVER_PASS@localhost:7151 -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $PEER1_USER --id.secret $PEER1_TLSCA_PASS --id.type peer -u https://localhost:7151  -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $PEER2_USER --id.secret $PEER2_TLSCA_PASS --id.type peer -u https://localhost:7151  -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $PEER3_USER --id.secret $PEER3_TLSCA_PASS --id.type peer -u https://localhost:7151  -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem
bin/fabric-ca-client register --id.name $PEERS_ADMIN_USER --id.secret $PEERS_ADMIN_TLSCA_PASS --id.type admin -u https://localhost:7151  -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem --id.attrs "hf.Registrar.Roles=client,hf.Registrar.Attributes=*,hf.Revoker=true,hf.GenCRL=true,admin=true:ecert,abac.init=true:ecert"

#Generate peers local TLS
bin/fabric-ca-client enroll -u https://$PEER1_USER:$PEER1_TLSCA_PASS@localhost:7151 --enrollment.profile tls -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/tls --csr.hosts peer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEER2_USER:$PEER2_TLSCA_PASS@localhost:7151 --enrollment.profile tls -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/tls --csr.hosts peer2,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEER3_USER:$PEER3_TLSCA_PASS@localhost:7151 --enrollment.profile tls -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/tls --csr.hosts peer3,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEERS_ADMIN_USER:$PEERS_ADMIN_TLSCA_PASS@localhost:7151 --enrollment.profile tls -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/tls

#Rename TLS keystore
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/tls/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/tls/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/tls/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/tls/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/tls/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/tls/keystore/priv_sk

mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/tls/tlscacerts/peers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/tls/tlscacerts/peers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/tls/tlscacerts/peers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/tls/tlscacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/tls/tlscacerts/peers-tlsca-cert.pem


#Register orderers identities with the orderers-rca
bin/fabric-ca-client enroll -u https://$ORDERERS_RCA_SERVER_USER:$ORDERERS_RCA_SERVER_PASS@localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem
bin/fabric-ca-client register --id.name $ORDERER1_USER --id.secret $ORDERER1_RCA_PASS --id.type orderer -u https://localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem
bin/fabric-ca-client register --id.name $ORDERER2_USER --id.secret $ORDERER2_RCA_PASS --id.type orderer -u https://localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem
bin/fabric-ca-client register --id.name $ORDERER3_USER --id.secret $ORDERER3_RCA_PASS --id.type orderer -u https://localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem
bin/fabric-ca-client register --id.name $ORDERERS_ADMIN_USER --id.secret $ORDERERS_ADMIN_RCA_PASS --id.type admin -u https://localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem

#Generate orderers local MSP
bin/fabric-ca-client enroll -u https://$ORDERER1_USER:$ORDERER1_RCA_PASS@localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp --csr.hosts orderer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERER2_USER:$ORDERER2_RCA_PASS@localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp --csr.hosts orderer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERER3_USER:$ORDERER3_RCA_PASS@localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp --csr.hosts orderer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$ORDERERS_ADMIN_USER:$ORDERERS_ADMIN_RCA_PASS@localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp

#Generate root msp for orderers. Will be saved in genesis.block
#fill msp/cacerts
bin/fabric-ca-client getcacert -u https://localhost:7152 -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp
#fill msp/tlscacerts
bin/fabric-ca-client getcacert -u https://localhost:7150 -H ${PWD}/data/orderers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-tlsca-cert.pem -M ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp --enrollment.profile tls
#fill msp/admincerts
bin/fabric-ca-client certificate list --id $ORDERERS_ADMIN_USER -H ${PWD}/data/orderers-rca-admin/ --tls.certfiles ${PWD}/data/certs/orderers-rca-cert.pem --store ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/admincerts

mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/tlscacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/tlscacerts/orderers-tlsca-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/cacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/cacerts/orderers-msp-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp/cacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp/cacerts/orderers-msp-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp/cacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp/cacerts/orderers-msp-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp/cacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp/cacerts/orderers-msp-cert.pem
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp/cacerts/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp/cacerts/orderers-msp-cert.pem

cp config/orderers-config.yaml ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/msp/config.yaml
cp config/orderers-config.yaml ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp/config.yaml
cp config/orderers-config.yaml ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp/config.yaml
cp config/orderers-config.yaml ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp/config.yaml
cp config/orderers-config.yaml ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp/config.yaml


#Rename MSP keystore
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp/keystore/* ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/$ORDERERS_ADMIN_USER/msp/keystore/priv_sk

#Register peers identities with the peers-rca
bin/fabric-ca-client enroll -u https://$PEERS_RCA_SERVER_USER:$PEERS_RCA_SERVER_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem
bin/fabric-ca-client register --id.name $PEER1_USER --id.secret $PEER1_RCA_PASS --id.type peer -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem
bin/fabric-ca-client register --id.name $PEER2_USER --id.secret $PEER2_RCA_PASS --id.type peer -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem
bin/fabric-ca-client register --id.name $PEER3_USER --id.secret $PEER3_RCA_PASS --id.type peer -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem
bin/fabric-ca-client register --id.name $PEERS_ADMIN_USER --id.secret $PEERS_ADMIN_RCA_PASS --id.type admin -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem  --id.attrs "hf.Registrar.Roles=client,hf.Registrar.Attributes=*,hf.Revoker=true,hf.GenCRL=true,admin=true:ecert,abac.init=true:ecert"
bin/fabric-ca-client register --id.name $PEERS_API_USER --id.secret $PEERS_API_PASS --id.type client -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem

#Generate peers local MSP
bin/fabric-ca-client enroll -u https://$PEER1_USER:$PEER1_RCA_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp --csr.hosts peer1,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEER2_USER:$PEER2_RCA_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp --csr.hosts peer2,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEER3_USER:$PEER3_RCA_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp --csr.hosts peer3,localhost,host.docker.internal,172.17.0.1
bin/fabric-ca-client enroll -u https://$PEERS_ADMIN_USER:$PEERS_ADMIN_RCA_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp
bin/fabric-ca-client enroll -u https://$PEERS_API_USER:$PEERS_API_PASS@localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp

#Generate root msp for peers. Will be saved in genesis.block
#fill msp/cacerts
bin/fabric-ca-client getcacert -u https://localhost:7153 -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp
#rename certificate to peers-msp-cert.pem to match peers-config.yaml
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/cacerts/peers-msp-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp/cacerts/peers-msp-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp/cacerts/peers-msp-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp/cacerts/peers-msp-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp/cacerts/peers-msp-cert.pem
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp/cacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp/cacerts/peers-msp-cert.pem

#fill msp/tlscacerts
bin/fabric-ca-client getcacert -u https://localhost:7151 -H ${PWD}/data/peers-tlsca-admin/ --tls.certfiles ${PWD}/data/certs/peers-tlsca-cert.pem -M ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp --enrollment.profile tls
#fill msp/admincerts
bin/fabric-ca-client certificate list --id $PEERS_ADMIN_USER -H ${PWD}/data/peers-rca-admin/ --tls.certfiles ${PWD}/data/certs/peers-rca-cert.pem --store ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/admincerts

#Rename MSP keystore
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp/keystore/priv_sk
mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp/keystore/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp/keystore/priv_sk

mv ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/tlscacerts/* ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/tlscacerts/peers-tlsca-cert.pem

#Copy peers OUs configuration to peerOrganizations msp. Will be saved in genesis.block
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/msp/config.yaml
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_ADMIN_USER/msp/config.yaml
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/$PEERS_API_USER/msp/config.yaml
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER1_USER/msp/config.yaml
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER2_USER/msp/config.yaml
cp config/peers-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/$PEER3_USER/msp/config.yaml


#Generate genesis.block
bin/configtxgen -configPath=config/ -profile GuardOrdererGenesis -channelID system -outputBlock ./data/channel-artifacts/genesis.block

#Generate channel
bin/configtxgen -configPath=config/ -profile GuardChannel -outputCreateChannelTx ./data/channel-artifacts/channel.tx -channelID "guard"

#Generate anchor tx for guard peer
bin/configtxgen -configPath=config/ -profile GuardChannel -outputAnchorPeersUpdate ./data/channel-artifacts/guardAnchors.tx  -channelID "guard" -asOrg GuardMSP

cp data/channel-artifacts/genesis.block data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER1_USER
cp data/channel-artifacts/genesis.block data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER2_USER
cp data/channel-artifacts/genesis.block data/crypto-config/ordererOrganizations/guard-project.eu/orderers/$ORDERER3_USER

docker run -d -p 7051:7051 \
--name orderer1 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:8443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e ORDERER_OPERATIONS_LISTENADDRESS='localhost:8443' \
-e ORDERER_METRICS_PROVIDER='prometheus' \
-e ORDERER_GENERAL_GENESISFILE='/var/hyperledger/orderer/genesis.block' \
-e ORDERER_GENERAL_BOOTSTRAPMETHOD='file' \
-e ORDERER_CHANNELPARTICIPATION_ENABLED='true' \
-e ORDERER_GENERAL_LISTENADDRESS='0.0.0.0' \
-e ORDERER_GENERAL_LISTENPORT='7051' \
-e ORDERER_GENERAL_LOCALMSPDIR='/var/hyperledger/orderer/msp' \
-e ORDERER_GENERAL_LOCALMSPID='GuardOrdererMSP' \
-e ORDERER_GENERAL_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_TLS_ENABLED='true' \
-e ORDERER_GENERAL_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_GENERAL_CLUSTER_CLIENTCERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_CLUSTER_CLIENTPRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_CLUSTER_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_ADMIN_TLS_ENABLED='true' \
-e ORDERER_ADMIN_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_ADMIN_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CLIENTROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_LISTENADDRESS='localhost:9050' \
-v ${PWD}/data/orderer1-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/${ORDERER1_USER}:/var/hyperledger/orderer \
hyperledger/fabric-orderer:amd64-2.3.1 orderer





docker run -d -p 7052:7052 \
--name orderer2 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:8443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e ORDERER_OPERATIONS_LISTENADDRESS='localhost:8443' \
-e ORDERER_METRICS_PROVIDER='prometheus' \
-e ORDERER_GENERAL_GENESISFILE='/var/hyperledger/orderer/genesis.block' \
-e ORDERER_GENERAL_BOOTSTRAPMETHOD='file' \
-e ORDERER_CHANNELPARTICIPATION_ENABLED='true' \
-e ORDERER_GENERAL_LISTENADDRESS='0.0.0.0' \
-e ORDERER_GENERAL_LISTENPORT='7052' \
-e ORDERER_GENERAL_LOCALMSPDIR='/var/hyperledger/orderer/msp' \
-e ORDERER_GENERAL_LOCALMSPID='GuardOrdererMSP' \
-e ORDERER_GENERAL_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_TLS_ENABLED='true' \
-e ORDERER_GENERAL_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_GENERAL_CLUSTER_CLIENTCERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_CLUSTER_CLIENTPRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_CLUSTER_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_ADMIN_TLS_ENABLED='true' \
-e ORDERER_ADMIN_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_ADMIN_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CLIENTROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_LISTENADDRESS='localhost:9050' \
-v ${PWD}/data/orderer2-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/${ORDERER2_USER}:/var/hyperledger/orderer \
hyperledger/fabric-orderer:amd64-2.3.1 orderer



docker run -d -p 7053:7053 \
--name orderer3 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:8443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e ORDERER_OPERATIONS_LISTENADDRESS='localhost:8443' \
-e ORDERER_METRICS_PROVIDER='prometheus' \
-e ORDERER_GENERAL_GENESISFILE='/var/hyperledger/orderer/genesis.block' \
-e ORDERER_GENERAL_BOOTSTRAPMETHOD='file' \
-e ORDERER_CHANNELPARTICIPATION_ENABLED='true' \
-e ORDERER_GENERAL_LISTENADDRESS='0.0.0.0' \
-e ORDERER_GENERAL_LISTENPORT='7053' \
-e ORDERER_GENERAL_LOCALMSPDIR='/var/hyperledger/orderer/msp' \
-e ORDERER_GENERAL_LOCALMSPID='GuardOrdererMSP' \
-e ORDERER_GENERAL_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_TLS_ENABLED='true' \
-e ORDERER_GENERAL_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_GENERAL_CLUSTER_CLIENTCERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_GENERAL_CLUSTER_CLIENTPRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_GENERAL_CLUSTER_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CERTIFICATE='/var/hyperledger/orderer/tls/signcerts/cert.pem' \
-e ORDERER_ADMIN_TLS_ENABLED='true' \
-e ORDERER_ADMIN_TLS_PRIVATEKEY='/var/hyperledger/orderer/tls/keystore/priv_sk' \
-e ORDERER_ADMIN_TLS_ROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_TLS_CLIENTROOTCAS='[/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem]' \
-e ORDERER_ADMIN_LISTENADDRESS='localhost:9050' \
-v ${PWD}/data/orderer3-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/orderers/${ORDERER3_USER}:/var/hyperledger/orderer \
hyperledger/fabric-orderer:amd64-2.3.1 orderer

#Wait for orderers's to start
set +x 
wait_for_logs_occurs orderer1 "Beginning to serve requests"
wait_for_logs_occurs orderer2 "Beginning to serve requests"
wait_for_logs_occurs orderer3 "Beginning to serve requests"
set -x


chmod +x builders/*

cp config/builders-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER1_USER}/core.yaml
cp config/builders-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER2_USER}/core.yaml
cp config/builders-config.yaml ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER3_USER}/core.yaml

#Start peers
docker run -d -p 8051:8051 \
--name peer1 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:9443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_LISTENADDRESS='0.0.0.0:8051' \
-e CORE_PEER_ADDRESS='host.docker.internal:8051' \
-e CORE_PEER_GOSSIP_EXTERNALENDPOINT='host.docker.internal:8050' \
-e CORE_PEER_CHAINCODELISTENADDRESS='localhost:9050' \
-e CORE_PEER_GOSSIP_USELEADERELECTION='true' \
-e CORE_PEER_GOSSIP_BOOTSTRAP='host.docker.internal:8052 host.docker.internal:8053' \
-e CORE_PEER_ID='peer1' \
-e FABRIC_CFG_PATH='/var/hyperledger/fabric' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_PROFILE_ENABLED='true' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e CORE_OPERATIONS_LISTENADDRESS='localhost:9443' \
-e CORE_METRICS_PROVIDER='prometheus' \
-v ${PWD}/data/peer1-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER1_USER}:/var/hyperledger/fabric \
-v ${PWD}/builders:/builders/external/bin \
hyperledger/fabric-peer:amd64-2.3.1 peer node start



docker run -d -p 8052:8052 \
--name peer2 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:9443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_LISTENADDRESS='0.0.0.0:8052' \
-e CORE_PEER_ADDRESS='host.docker.internal:8052' \
-e CORE_PEER_GOSSIP_EXTERNALENDPOINT='host.docker.internal:8052' \
-e CORE_PEER_CHAINCODELISTENADDRESS='localhost:9050' \
-e CORE_PEER_GOSSIP_USELEADERELECTION='true' \
-e CORE_PEER_GOSSIP_BOOTSTRAP='host.docker.internal:8051 host.docker.internal:8053' \
-e CORE_PEER_ID='peer2' \
-e FABRIC_CFG_PATH='/var/hyperledger/fabric' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_PROFILE_ENABLED='true' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e CORE_OPERATIONS_LISTENADDRESS='localhost:9443' \
-e CORE_METRICS_PROVIDER='prometheus' \
-v ${PWD}/data/peer2-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER2_USER}:/var/hyperledger/fabric \
-v ${PWD}/builders:/builders/external/bin \
hyperledger/fabric-peer:amd64-2.3.1 peer node start


docker run -d -p 8053:8053 \
--name peer3 \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:9443/healthz || exit 1 ' \
--health-interval=2s \
--restart always \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_LISTENADDRESS='0.0.0.0:8053' \
-e CORE_PEER_ADDRESS='host.docker.internal:8053' \
-e CORE_PEER_GOSSIP_EXTERNALENDPOINT='host.docker.internal:8053' \
-e CORE_PEER_CHAINCODELISTENADDRESS='localhost:9050' \
-e CORE_PEER_GOSSIP_USELEADERELECTION='true' \
-e CORE_PEER_GOSSIP_BOOTSTRAP='host.docker.internal:8051 host.docker.internal:8052' \
-e CORE_PEER_ID='peer3' \
-e FABRIC_CFG_PATH='/var/hyperledger/fabric' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_PROFILE_ENABLED='true' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e CORE_OPERATIONS_LISTENADDRESS='localhost:9443' \
-e CORE_METRICS_PROVIDER='prometheus' \
-v ${PWD}/data/peer3-data:/var/hyperledger/production \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/peers/${PEER3_USER}:/var/hyperledger/fabric \
-v ${PWD}/builders:/builders/external/bin \
hyperledger/fabric-peer:amd64-2.3.1 peer node start



#create channel and join other peers
docker run -it --rm \
--name peers-cli \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_ID='peers-cli' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_MSPCONFIGPATH='/var/hyperledger/admin/msp' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e GOPATH='/opt/gopath' \
-e ORDERER_CA='/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem' \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/tls:/var/hyperledger/fabric/tls \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/msp:/var/hyperledger/admin/msp \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/${ORDERERS_ADMIN_USER}/tls/tlscacerts:/var/hyperledger/orderer/tls/tlscacerts \
-v ${PWD}/data/channel-artifacts:/channel-artifacts \
-v ${PWD}/chaincode/packaging:/chaincode \
hyperledger/fabric-tools:amd64-2.3.1 sh -c '
CORE_PEER_ADDRESS=host.docker.internal:8051 peer channel create -o host.docker.internal:7051 -c guard -f /channel-artifacts/channel.tx --tls true --cafile $ORDERER_CA
sleep 3
CORE_PEER_ADDRESS=host.docker.internal:8051 peer channel join -b guard.block
CORE_PEER_ADDRESS=host.docker.internal:8052 peer channel join -b guard.block
CORE_PEER_ADDRESS=host.docker.internal:8053 peer channel join -b guard.block
'


#Build chaincode image
cd chaincode/src
docker build -t chaincode/storage-chaincode:1.0 .

#Pack chaincode image
cd ../packaging/
tar cfz code.tar.gz connection.json
tar cfz storage-chaincode-guard.tgz code.tar.gz metadata.json

cd ../..




# install chaincode packaging
docker run -it --rm \
--name peers-cli \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_ID='peers-cli' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_MSPCONFIGPATH='/var/hyperledger/admin/msp' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e GOPATH='/opt/gopath' \
-e ORDERER_CA='/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem' \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/tls:/var/hyperledger/fabric/tls \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/msp:/var/hyperledger/admin/msp \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/${ORDERERS_ADMIN_USER}/tls/tlscacerts:/var/hyperledger/orderer/tls/tlscacerts \
-v ${PWD}/data/channel-artifacts:/channel-artifacts \
-v ${PWD}/chaincode/packaging:/chaincode \
hyperledger/fabric-tools:amd64-2.3.1 sh -c '
CORE_PEER_ADDRESS=host.docker.internal:8051 peer lifecycle chaincode install /chaincode/storage-chaincode-guard.tgz
CORE_PEER_ADDRESS=host.docker.internal:8052 peer lifecycle chaincode install /chaincode/storage-chaincode-guard.tgz
CORE_PEER_ADDRESS=host.docker.internal:8053 peer lifecycle chaincode install /chaincode/storage-chaincode-guard.tgz
'


packageId=$(docker run -it --rm \
--name peers-cli \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_ID='peers-cli' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_MSPCONFIGPATH='/var/hyperledger/admin/msp' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e GOPATH='/opt/gopath' \
-e ORDERER_CA='/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem' \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/tls:/var/hyperledger/fabric/tls \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/msp:/var/hyperledger/admin/msp \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/${ORDERERS_ADMIN_USER}/tls/tlscacerts:/var/hyperledger/orderer/tls/tlscacerts \
-v ${PWD}/data/channel-artifacts:/channel-artifacts \
-v ${PWD}/chaincode/packaging:/chaincode \
hyperledger/fabric-tools:amd64-2.3.1 sh -c 'CORE_PEER_ADDRESS=host.docker.internal:8051 peer lifecycle chaincode queryinstalled' | grep "storage-chaincode" | awk -F[,\ ] '{print $3}')





docker run -d -p 9050:9050 \
--name chaincode-storage \
--restart always \
-e CHAINCODE_CCID=${packageId} \
-e CHAINCODE_ADDRESS='0.0.0.0:9050' \
chaincode/storage-chaincode:1.0 

docker run -it --rm \
--name peers-cli \
-e FABRIC_LOGGING_SPEC='INFO' \
-e CORE_PEER_ID='peers-cli' \
-e CORE_PEER_LOCALMSPID='GuardMSP' \
-e CORE_PEER_MSPCONFIGPATH='/var/hyperledger/admin/msp' \
-e CORE_PEER_TLS_CERT_FILE='/var/hyperledger/fabric/tls/signcerts/cert.pem' \
-e CORE_PEER_TLS_ENABLED='true' \
-e CORE_PEER_TLS_KEY_FILE='/var/hyperledger/fabric/tls/keystore/priv_sk' \
-e CORE_PEER_TLS_ROOTCERT_FILE='/var/hyperledger/fabric/tls/tlscacerts/peers-tlsca-cert.pem' \
-e GOPATH='/opt/gopath' \
-e ORDERER_CA='/var/hyperledger/orderer/tls/tlscacerts/orderers-tlsca-cert.pem' \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/tls:/var/hyperledger/fabric/tls \
-v ${PWD}/data/crypto-config/peerOrganizations/guard-project.eu/users/${PEERS_ADMIN_USER}/msp:/var/hyperledger/admin/msp \
-v ${PWD}/data/crypto-config/ordererOrganizations/guard-project.eu/users/${ORDERERS_ADMIN_USER}/tls/tlscacerts:/var/hyperledger/orderer/tls/tlscacerts \
-v ${PWD}/data/channel-artifacts:/channel-artifacts \
-v ${PWD}/chaincode/packaging:/chaincode \
hyperledger/fabric-tools:amd64-2.3.1 sh -c '
CORE_PEER_ADDRESS=host.docker.internal:8051 peer lifecycle chaincode approveformyorg --channelID guard --name storage-chaincode --version 1.0 --init-required --package-id '$packageId' --sequence 1 -o host.docker.internal:7051 --tls --cafile $ORDERER_CA
sleep 3
CORE_PEER_ADDRESS=host.docker.internal:8051 peer lifecycle chaincode commit -o host.docker.internal:7051 --channelID guard --name storage-chaincode --version 1.0 --sequence 1 --init-required --tls true --cafile $ORDERER_CA --peerAddresses host.docker.internal:8051 --tlsRootCertFiles $CORE_PEER_TLS_ROOTCERT_FILE
sleep 3
CORE_PEER_ADDRESS=host.docker.internal:8051 peer chaincode invoke -o host.docker.internal:7051 --isInit --tls true --cafile $ORDERER_CA -C guard -n storage-chaincode --peerAddresses host.docker.internal:8051 --tlsRootCertFiles $CORE_PEER_TLS_ROOTCERT_FILE -c '"'"'{"Args":[""]}'"'"' --waitForEvent
'

#To test chaincode run CLI
#To test save function
#CORE_PEER_ADDRESS=host.docker.internal:8051 peer chaincode invoke -o host.docker.internal:7051 --tls true --cafile $ORDERER_CA -C guard -n storage-chaincode --peerAddresses host.docker.internal:8051 --tlsRootCertFiles $CORE_PEER_TLS_ROOTCERT_FILE -c '{"Args":["save", "test"]}' --waitForEvent

#To test retrieve function, change txId to payload returend from command above
#CORE_PEER_ADDRESS=host.docker.internal:8051 peer chaincode invoke -o host.docker.internal:7051 --tls true --cafile $ORDERER_CA -C guard -n storage-chaincode --peerAddresses host.docker.internal:8051 --tlsRootCertFiles $CORE_PEER_TLS_ROOTCERT_FILE -c '{"Args":["retrieve", "txId"]}' --waitForEvent


#Build blockchain-connector
cd blockchain-connector
docker build -t blockchain-connector:1.0 . --no-cache
cd ..

docker run -d -p 8080:8080 \
--name blockchain-connector \
--health-cmd='wget --no-verbose --tries=1 --spider http://localhost:8080/health || exit 1 ' \
--health-interval=2s \
--restart always \
-e ORDERERS_URLS=host.docker.internal:7051,host.docker.internal:7052,host.docker.internal:7053 \
-e PEER_URLS=host.docker.internal:8051,host.docker.internal:8052,host.docker.internal:8053 \
-e PEERS_USER_PRIVATE_KEY_PATH=/crypto-materials/peerOrganizations/guard-project.eu/users/peers-api/msp/keystore/priv_sk \
-e PEERS_USER_CERTIFICATE_PATH=/crypto-materials/peerOrganizations/guard-project.eu/users/peers-api/msp/signcerts/cert.pem \
-e PEER_TLSCA_PEM_PATH=/crypto-materials/peerOrganizations/guard-project.eu/msp/tlscacerts/peers-tlsca-cert.pem \
-e ORDERER_TLSCA_PEM_PATH=/crypto-materials/ordererOrganizations/guard-project.eu/msp/tlscacerts/orderers-tlsca-cert.pem \
-v ${PWD}/data/crypto-config:/crypto-materials \
-v ${PWD}/certs:/certs \
blockchain-connector:1.0


echo "DONE"

