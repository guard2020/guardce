package org.telematics.guard.aa;

public interface IKafkaConstants {
	String KAFKA_BROKERS = "kafka:9093";

	String CLIENT_ID="consumer-example";

	String GROUP_ID="foo";

	String TOPIC_NAME="test";

	Integer MAX_NO_MESSAGE_FOUND_COUNT=100;

	String OFFSET_RESET_LATEST="latest";

	String OFFSET_RESET_EARLIER="earliest";

	Integer MAX_POLL_RECORDS=1;

	String KAFKA_SECURITY_PROTOCOL = "SASL_SSL";

	String KAFKA_SSL_TRUSTSTORE_LOCATION = "/var/private/ssl/truststore.jks";
	String KAFKA_SSL_TRUSTSTORE_PASSWORD = "javacaps";

	String KAFKA_SSL_KEYSTORE_LOCATION = "/var/private/ssl/consumer_example.jks";
	String KAFKA_SSL_KEYSTORE_PASSWORD = "javacaps";
	String KAFKA_SSL_KEY_PASSWORD = "javacaps";

	// NOTICE:
	// Host name verification of servers is enabled by default for client connections as well
	// as inter-broker connections to prevent man-in-the-middle attacks.
	// Source: https://docs.confluent.io/platform/current/security/security_tutorial.html
	String KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM = "";

	String KAFKA_SASL_MECHANISM = "OAUTHBEARER";
	String KAFKA_SASL_JAAS_CONFIG = "org.apache.kafka.common.security.oauthbearer.OAuthBearerLoginModule required;";
	String KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS = "org.telematics.guard.aa.kafka.liboauthbearer.OAuthAuthenticateLoginCallbackHandler";
}
