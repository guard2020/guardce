# AA Module Docker Environment

Ready-to-build Docker Containers to quickly create an environment for AA Module.
This environment includes:

* An [**Identity Provider (IdP)**](identity_provider/), based on open source project WSO2 Identity Server.
* A preconfigured [**Kafka Reference Broker**](kafka_confluent/) with authentication delegation provided by **OAuth2 Extension** and **mTLS**.
* A preconfigured [**Zookeeper**](kafka_confluent/compose.yml) instance for Kafka.
* [**Kafka Java Consumer Example**](consumer_example/) as a reference for subscribing to a Kafka Topic.
* [**Kafka Java Producer Example**](producer_example/) as a reference for publishing on a Kafka Topic.
* [**Kafka Spring Client Example**](kafka_spring_client_oauth/) as a reference to integrates OAuth2 Extension on Java Spring projects.
* [**Kafka Python Client Example**](kafka_oauth_python/) as a Kafka OAuth2 Client reference for python-based projects.
* [**Kafka mTLS Client Example**](kafka_tls_python/) as a demonstration of mutual TLS authentication. Some scripts are provided to manage authorization of TLS clients.
* [**Google OAuth2 Reference Example**](service_google_oauth/) as a demonstration of federated authentication through 3rd party IdP.
* [**Logstash Reference Example**](logstash_oauth/) a modified Logstash version which supports AA Module integration.

## Important note for Windows users
Please ensure that the git option `core.autocrlf` is set to `input`.
To apply this parameter execute the following command:
```
> git config --global core.autocrlf input
```
Then re-clone again the repository in your PC.

## Quick start
First of all, ensure you have Docker installed and Docker Deamon is running. Then run the following command in your shell to start-up a service:
### On Windows
```
> ./docker-compose.ps1 up service_name
```
### On Linux (with root permission)
```
> ./docker-compose.sh up service_name
```
The main services you need to start initially are `idp`, `zookeeper` and `kafka1`.

## Allow services to perform OAuth2 authentication
### First method:
Add the following row to the `identity_provider/bootstrap/bootstrap.sh` script:
```
export SP_NAME="SERVICE DESCRIPTOR"
export SP_OAUTH2_KEY="SERVICE CLIENT_ID"
export SP_OAUTH2_SECRET="SERVICE SECRET"
register_oauth_service_provider $SP_NAME $WSO2_USERNAME $WSO2_PASSWORD $SP_OAUTH2_KEY $SP_OAUTH2_SECRET
```
Then re-run the IdP container with the --build option.
### Second method:
Use IdP web interface to the uri `https://localhost:10443` on the host machine.

## Manage authorization for mTLS Client
First of all, start-up the Kafka REST API by running `kafka_rest_proxy` container. Then use the python scripts into `kafka_rest_acl/` to manage mTLS permission.
In order to grant authorization to publish on a certain _topic-name_ run the following command where _cert-subject-name_ is the Subject Name contained in the Client x509 certificate:
```
> ./create_acl.py TOPIC topic-name LITERAL "User:cert-subject-name" '*' DESCRIBE
```
In the same way authorizations can be revoked:
```
> ./delete_acl.py TOPIC topic-name LITERAL "User:cert-subject-name" '*' DESCRIBE
```
## Useful commands
* `./docker-compose.sh up -d service_name`, run a container in the background.
* `./docker-compose.sh up --no-deps service_name`, run a container without starting linked services.
* `./docker-compose.sh down`, stop all containers the docker-compose.sh script refers to.
* `./docker-compose.sh ps`, list running containers.

## OAuth Environment Variables Configuration
Each integrated service that use the **Kafka OAuth Library** can be configured by setting Environment Variables in the compose file.
### Java-based modules

* `OAUTH_SERVER_BASE_URI`, a String that contains the main URI to the OAuth Endpoint of the Identity Provider, like `https://idp:10443/oauth2/`.
* `OAUTH_SERVER_TOKEN_ENDPOINT_PATH`, the extension of the `OAUTH_SERVER_BASE_URI` to reach the Token Endpoint, like `/token`. This endpoint is used to get the JWT or refresh it.
* `OAUTH_SERVER_INTROSPECTION_ENDPOINT_PATH`, the extension of the `OAUTH_SERVER_BASE_URI` to reach the Token Introspection Endpoint, like `/introspect`. This endpoint is used to decode and unpack a valid JWT.
* `OAUTH_SERVER_CLIENT_ID`, a String containing the registered Client ID for this service on the Identity Provider.
* `OAUTH_SERVER_CLIENT_SECRET`, a String containing the used secret shared with the Identity Provider.
* `OAUTH_SERVER_GRANT_TYPE`, a String containing the OAuth2 method to be used to get a grant from the Identity Provider, like `client_credentials`.
* `OAUTH_SERVER_SCOPES`, a String containing the list of Kafka authorizations to grant for the service.
* `OAUTH_SERVER_ACCEPT_UNSECURE_SERVER`, a Boolean to contact a foreign TLS Endpoint of the Identity Provider with untrusted Server Certificate.
* `OAUTH_SERVER_ENFORCE`, a Boolean to enforce or not the AA Module protection. In case of `false`, authentication/authorization errors will be reported, but not enforced. **This is useful to grant access to Kafka from untrusted/unauthenticated clients. Use it, with caution, for testing purposes only.** This flag should ALWAYS be put to `true`.

### Python-based modules
Python-based modules allow you to instantiate multiple kafka clients in a single container by indicating the instance number instead of `n`:

* `OAUTH2_INSTANCE_n_CLIENT_ID`, a String containing the registered Client ID for this service on the Identity Provider (Grant type is set by default in _client credentials_ mode).
* `OAUTH2_INSTANCE_n_SECRET`, a String containing the used secret shared with the Identity Provider.
* `OAUTH2_INSTANCE_n_TOKEN_URI`, a String that contains the Identity Provider Token Endpoint URI. It's used to get the JWT or refresh it.
* `OAUTH2_INSTANCE_n_IDP_TLS`, a String that indicates the name of the truststore containing the certificate of the CA used to validate the IdP certificate.
* `OAUTH2_INSTANCE_n_SCOPES`, a String containing the list of Kafka authorizations to grant for the service.

######