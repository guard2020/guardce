# Guard Community Edition (guardce)
GUARD Project Platform Community Edition 


## How to run GUARD PLATFORM on cloud machine (or Linux virtual machine on PC - Minimal Edition only for demo purposes)

1) Copy .env and fill the environment variables with your local value. In .env must be set {GUARD_REPOSITORY} with `<repository-name>` , {GUARD_SERVER}  and {GUARD_SERVER_ADDRESS} with proper value. Other instruction and example inside the .env file.
2) Copy guard_cloud/docker-compose-cloud-*.yml and in parent directory prior to execute. 
3) Run ./volumes/build-volumes-min.sh {VOLUME_DIR} to create volumes (for minimal edition) or run volumes/build-volumes.sh.
The minimal edition doesn't require any TLS certificate to run. 

```console
$ docker-compose -f docker-compose-cloud-min.yml up -d [service] (for minimal edition)
or
$ docker-compose -f docker-compose-cloud-std.yml up -d [service] (for standard edition)

```

## How to run GUARD PLATFORM on openshift platform

Please before take a look on Volumes part.

```console
$ cd guard-openshift
$ ./start.sh
```


## Volumes for standard edition

Preliminary operatione on volumes (for cloud) or persistent storage (Openshift)
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
