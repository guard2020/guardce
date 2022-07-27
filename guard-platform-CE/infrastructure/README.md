Prerequisites
=============

Install Flux 1.21
-----------------
Please install fluxctl

**Namespace: flux**

```bash
kubectl create ns flux

fluxctl install \
--git-email=flux-infrastructure-noreply@guard-project.eu \
--git-url=git@gitlab.com:guard2/infrastructure.git \
--git-path=base \
--git-readonly \
--manifest-generation=true \
--namespace=flux | kubectl apply -f -

helm repo add fluxcd https://charts.fluxcd.io

helm repo update

helm upgrade -i helm-operator fluxcd/helm-operator \
--set git.ssh.secretName=flux-git-deploy \
--set helm.versions=v3 \
--namespace flux
```

## Certificates

### Kubernetes Ingress
cert-manager provides direct integration with Kubernetes Ingress by configuring an annotation on the Ingress object. If this method is used, the Ingress must reside in the same namespace as the ingress deployment, as secrets will only be read within the same namespace.


```yaml
apiVersion: extensions/v1beta1
kind: Ingress
metadata:
  name: ingress
  annotations:
    kubernetes.io/ingress.class: nginx
    cert-manager.io/cluster-issuer: letsencrypt-prod
spec:
    ...
```
### External Access
Make sure anything in front of the LoadBalancer has the proper ports open.

# Services needing a "manual" step

## external-dns
**namespace: external-dns**  
[External-DNS project information](https://github.com/kubernetes-sigs/external-dns "Project Info")

Example for Openstack:
```yaml
---
apiVersion: v1
kind: Secret
metadata:
  name: external-dns-designate
  namespace: external-dns
type: Opaque
data:
  username: # BASE64 encoded designate OS_USERNAME
  password: # BASE64 encoded designate OS_PASSWORD

```
# Operators
## Kafka
**namespace: operator**
[Kafka-Operator project information](https://strimzi.io/ "Strimzi Kafka")  

### Kafka CustomResources
**namespace: kafka**  

The following example will deploy a kafka cluster (3 replicas) with zookeeper (3 replicas) using the Ingress Resource as the entrypoint.  
The Kafka-Bootstrap URL is: kafka-bootstrap.guard.heisenbug.net:443 . It uses TLS and SCRAM-512.
```yaml
apiVersion: kafka.strimzi.io/v1beta1
kind: Kafka
metadata:
  name: guard-cluster
  namespace: kafka
spec:
  kafka:
    version: 2.6.0
    replicas: 3
    listeners:
      - name: plain
        port: 9092
        type: internal
        tls: false
        authentication:
          type: scram-sha-512
      - name: external
        port: 9094
        type: ingress
        tls: true
        authentication:
          type: scram-sha-512
        configuration:
          bootstrap:
            host: kafka-bootstrap.guard.heisenbug.net
          brokers:
          - broker: 0
            host: kafka-0.guard.heisenbug.net
          - broker: 1
            host: kafka-1.guard.heisenbug.net
          - broker: 2
            host: kafka-2.guard.heisenbug.net
          brokerCertChainAndKey:
            secretName: cert-kafka
            certificate: tls.crt
            key: tls.key
    authorization:
      type: simple
    config:
      auto.create.topics.enable: "false"
      offsets.topic.replication.factor: 3
      transaction.state.log.replication.factor: 3
      transaction.state.log.min.isr: 2
      log.message.format.version: "2.6"
    storage:
      type: jbod
      volumes:
      - id: 0
        type: persistent-claim
        size: 50Gi
        deleteClaim: false
  zookeeper:
    replicas: 3
    storage:
      type: persistent-claim
      size: 25Gi
      deleteClaim: false
  entityOperator:
    topicOperator: {}
    userOperator: {}

```
The following example creates a topic `chirpstack_as`. The `config` property is used to pass configuration.
```yaml
apiVersion: kafka.strimzi.io/v1beta1
kind: KafkaTopic
metadata:
  name: chirpstack-as
  namespace: kafka
  labels:
    strimzi.io/cluster: guard-cluster
spec:
  topicName: chirpstack_as
  partitions: 10
  replicas: 2
  config:
    retention.ms: 7200000
    segment.bytes: 1073741824
    message.format.version: "2.6"
 
```

Following example grants `READ`-ability on the topic `chirpstack_as` for user `guard-nask-0` belonging to the consumer group `nask`
```yaml
---
apiVersion: kafka.strimzi.io/v1beta1
kind: KafkaUser
metadata:
  name: guard-nask-0
  namespace: kafka
  labels:
    strimzi.io/cluster: guard-cluster
spec:
  authentication:
    type: scram-sha-512
  authorization:
    type: simple
    acls:
      - resource:
          type: topic
          name: chirpstack_as
          patternType: literal
        operation: Read
        host: "*"
      - resource:
          type: topic
          name: chirpstack_as
          patternType: literal
        operation: Describe
        host: "*"
      - resource:
          type: group
          name: nask
          patternType: literal
        operation: Read
        host: "*"

```

