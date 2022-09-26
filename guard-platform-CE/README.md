# Final Release


## Instructions for GUARD IMAGE BUILDER

Clone the repository

```console
git clone --recurse-submodules -v https://oauth2:XXXXXXXXXXXXXXXX@gitlab.com/guard2/guard-platform.git (for Guard repository you must have an access token)
```
Copy guard_builder/docker-compose-builder.yml and guard_builder/buildONE.sh in parent directory prior to execute 

```console
$ ./buildONE <repository-name> [<component-name>]
```


In guard_platform/.env must be set GUARD_REPOSITORY with '<repository-name>

Please be sure that all images are pushed on guard-repository.


## Instructions to run GUARD PLATFORM on cloud machine

Copy guard_cloud/docker-compose-cloud.yml and in parent directory prior to execute 

```console
$ docker-compose -f docker-compose-cloud.yml up -d [service]
```


In .env must be set GUARD_REPOSITORY with `<repository-name>` , GUARD_SERVER  and GUARD_SERVER_ADDRESS with proper value


## Instructions to run GUARD PLATFORM on openshift platform

```console
$ cd guard-openshift
$ ./start.sh
```


## Volumes

Preliminary operatione on volumes (for virtual) or persistent storage (Openshift)
Following volumes must be present for virtual (VOLUME_DIR in .env):
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

For Openshift platform, the storage for configuration file is made through secrets mechanism (in file start.sh)
For persistent storage, persistent volume must be available with suitable size:
- 10 volumes with 10Gb size
- 3 volumes with almost 100 Mb



Enjoy GUARD platform :-)


