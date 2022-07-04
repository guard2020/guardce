# Guard Community Edition (guardce)
GUARD Project Platform Community Edition 


## How to run GUARD PLATFORM on cloud machine (or Linux virtual machine on PC - Minimal Edition only for demo purposes)

Minimal hardware requirements for virtual machine: 4 VCPUs, 16G RAM.

1) Unzip the repo in a directory where you have rights with sudoers user.
```console 
unzip guardce-main.zip -d .
```
2) A new directory will be created: guardce-main. Please go to it.
```console
 cd guardce-main
```
3) Please copy the following files in this directory:
```console
cp guard_cloud/.env .
cp guard_cloud/docker-compose-cloud-min.yml .
```
4) Edit .env file. You have to set the {GUARD_SERVER} variable with your external IP address or DNS and {GUARD_SERVER_ADDRESS} with your internal IP address. All other values can be unchanged.
5) Run 
```console
bash ./volumes/build-volumes-min.sh {VOLUME_DIR}
```
to create required volumes. The {VOLUME_DIR} will be the root directory (eg: /opt/guard).
6) For elasticsearch, you need to run:
```console
sudo sysctl -w vm.max_map_count=262144
```
>The minimal edition doesn't require any TLS certificate to run. 
7) Start the framework:
```console
$ docker-compose -f docker-compose-cloud-min.yml up -d [service]
```
8) If all is OK and all containers are running, start final configuration:bash ./start_config.sh' for final configurations.
```console
bash ./start_config.sh'
```




## How to run GUARD PLATFORM on cloud machine (Standard Edition) - Require at least 32Gb RAM, 8 VCPU and 100Gb storage on disk

1) Unzip the repo in a directory where you have rights with sudoers user:  unzip guardce-main.zip -d .
2) cd guardce-main
3) cp guard_cloud/.env . (In .env must be set {GUARD_REPOSITORY} with `<repository-name>` , {GUARD_SERVER} and {GUARD_SERVER_ADDRESS} with proper value)
5) cp guard_cloud/docker-compose-cloud.yml . 
6) Run 'bash ./volumes/build-volumes.sh {VOLUME_DIR}' to create volumes
7) For elasticsearch, you need to run 'sudo sysctl -w vm.max_map_count=262144

And then:
```console
$ docker-compose -f docker-compose-cloud-std.yml up -d [service] (for standard edition)

```
If all is OK and all containers are running, you can run: 'bash ./guard_cloud/start_ini.sh' for final configurations.


## How to run GUARD PLATFORM on openshift platform

Please before take a look on Volumes part.

```console
$ cd guard-openshift
$ ./start.sh
```


## Volumes for standard edition

Preliminary operations on volumes (for cloud) or persistent storage (Openshift)
Following volumes must be present for cloud (VOLUME_DIR in .env):
- ${VOLUME_DIR}/certs/idp.jks (from aaa/identity_provider/idp.jks)
- ${VOLUME_DIR}/data/zookeeper/data (empty)
- ${VOLUME_DIR}/data/zookeeper/datalog (empty)
- ${VOLUME_DIR}/data/kafka1/data (empty)
- ${VOLUME_DIR}/data/kafka2/data (empty)
- ${VOLUME_DIR}/data/kafka3/data (empty)
- ${VOLUME_DIR}/kafka-cluster-ssl/secrets (suitable ssl certificate an kafka_server_jaas.conf from aaa/kafka_oauth/config)
- ${VOLUME_DIR}/logstash-cb/file-output (empty)
- ${VOLUME_DIR}/elastic/data01 (empty)
- ${VOLUME_DIR}/elastic/data02 (empty)
- ${VOLUME_DIR}/elastic/data03 (empty)
- ${VOLUME_DIR}/kibana/certs (suitable ssl certificate)
- ${VOLUME_DIR}/kibana/kibana.yml (from kibana/kibana.yml)
- ${VOLUME_DIR}/dashboard/certs (suitable ssl certificate)
- ${VOLUME_DIR}/alert/config.yaml

These directories were created by script guard-cloud/volumes/build-volumes.sh, that create also TLS certificates.

For Openshift platform, the storage for configuration file is made through secrets mechanism (in file start.sh)
For persistent storage, persistent volume must be available with suitable size:
- 10 volumes with 10Gb size
- 3 volumes with almost 100 Mb



Enjoy GUARD platform :-)
