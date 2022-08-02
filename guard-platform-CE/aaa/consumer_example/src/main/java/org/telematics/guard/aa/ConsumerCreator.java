package org.telematics.guard.aa;

import org.apache.kafka.clients.consumer.Consumer;
import org.apache.kafka.clients.consumer.ConsumerConfig;
import org.apache.kafka.clients.consumer.KafkaConsumer;
import org.apache.kafka.common.serialization.LongDeserializer;
import org.apache.kafka.common.serialization.StringDeserializer;
import org.telematics.guard.aa.kafka.liboauthbearer.EnvironmentVariablesUtil;

import java.util.Collections;
import java.util.Properties;

public class ConsumerCreator {

	// Configuration Keys
	public static final String CONF_KAFKA_SECURITY_PROTOCOL = "security.protocol";
	public static final String CONF_KAFKA_SSL_TRUSTSTORE_LOCATION = "ssl.truststore.location";
	public static final String CONF_KAFKA_SSL_TRUSTSTORE_PASSWORD = "ssl.truststore.password";
	public static final String CONF_KAFKA_SSL_KEYSTORE_LOCATION = "ssl.keystore.location";
	public static final String CONF_KAFKA_SSL_KEYSTORE_PASSWORD = "ssl.keystore.password";
	public static final String CONF_KAFKA_SSL_KEY_PASSWORD = "ssl.key.password";
	public static final String CONF_KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM = "ssl.endpoint.identification.algorithm";
	public static final String CONF_KAFKA_SASL_MECHANISM = "sasl.mechanism";
	public static final String CONF_KAFKA_SASL_JAAS_CONFIG = "sasl.jaas.config";
	public static final String CONF_KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS = "sasl.login.callback.handler.class";
	// Environment Variable Keys for Configuration Override
	public static final String KAFKA_BROKERS_ENV_VAR = "KAFKA_BROKERS";
	public static final String CLIENT_ID_ENV_VAR = "CLIENT_ID";
	public static final String GROUP_ID_ENV_VAR = "GROUP_ID";
	public static final String KAFKA_SECURITY_PROTOCOL_ENV_VAR = "KAFKA_SECURITY_PROTOCOL";
	public static final String KAFKA_SSL_TRUSTSTORE_LOCATION_ENV_VAR = "KAFKA_SSL_TRUSTSTORE_LOCATION";
	public static final String KAFKA_SSL_TRUSTSTORE_PASSWORD_ENV_VAR = "KAFKA_SSL_TRUSTSTORE_PASSWORD";
	public static final String KAFKA_SSL_KEYSTORE_LOCATION_ENV_VAR = "KAFKA_SSL_KEYSTORE_LOCATION";
	public static final String KAFKA_SSL_KEYSTORE_PASSWORD_ENV_VAR = "KAFKA_SSL_KEYSTORE_PASSWORD";
	public static final String KAFKA_SSL_KEY_PASSWORD_ENV_VAR = "KAFKA_SSL_KEY_PASSWORD";
	public static final String KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM_ENV_VAR = "KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM";
	public static final String KAFKA_SASL_MECHANISM_ENV_VAR = "KAFKA_SASL_MECHANISM";
	public static final String KAFKA_SASL_JAAS_CONFIG_ENV_VAR = "KAFKA_SASL_JAAS_CONFIG";
	public static final String KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS_ENV_VAR = "KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS";

	public static Consumer<Long, String> createConsumer() {
		final Properties props = new Properties();

		final String confKafkaBrokers = EnvironmentVariablesUtil.getStringEnvironmentVariable(
			KAFKA_BROKERS_ENV_VAR,
			IKafkaConstants.KAFKA_BROKERS);
		final String confClientId = EnvironmentVariablesUtil.getStringEnvironmentVariable(
			CLIENT_ID_ENV_VAR,
			IKafkaConstants.CLIENT_ID);
		final String confGroupId = EnvironmentVariablesUtil.getStringEnvironmentVariable(
			GROUP_ID_ENV_VAR,
			IKafkaConstants.GROUP_ID);
		final String confSecurityProtocol = EnvironmentVariablesUtil.getStringEnvironmentVariable(
			KAFKA_SECURITY_PROTOCOL_ENV_VAR,
			IKafkaConstants.KAFKA_SECURITY_PROTOCOL);

		props.put(ConsumerConfig.BOOTSTRAP_SERVERS_CONFIG, confKafkaBrokers);
		props.put(ConsumerConfig.CLIENT_ID_CONFIG, confClientId);
		props.put(ConsumerConfig.GROUP_ID_CONFIG, confGroupId);
		props.put(ConsumerConfig.KEY_DESERIALIZER_CLASS_CONFIG, LongDeserializer.class.getName());
		props.put(ConsumerConfig.VALUE_DESERIALIZER_CLASS_CONFIG, StringDeserializer.class.getName());
		props.put(ConsumerConfig.MAX_POLL_RECORDS_CONFIG, IKafkaConstants.MAX_POLL_RECORDS);
		props.put(ConsumerConfig.ENABLE_AUTO_COMMIT_CONFIG, "false");
		props.put(ConsumerConfig.AUTO_OFFSET_RESET_CONFIG, IKafkaConstants.OFFSET_RESET_EARLIER);

		// SASL, TLS, OAuth Settings | Suggested: PLAINTEXT, SSL, SASL_SSL
		props.put(CONF_KAFKA_SECURITY_PROTOCOL, confSecurityProtocol);

		// TLS+Truststore configuration to trust Kafka Brokers
		if (confSecurityProtocol.contains("SSL")) {
			final String confTruststoreLocation = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_TRUSTSTORE_LOCATION_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_TRUSTSTORE_LOCATION);
			final String confTruststorePassword = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_TRUSTSTORE_PASSWORD_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_TRUSTSTORE_PASSWORD);
			final String confEndpointIdentificationAlgorithm = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM);
			final String confKeystoreLocation = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_KEYSTORE_LOCATION_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_KEYSTORE_LOCATION);
			final String confKeystorePassword = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_KEYSTORE_PASSWORD_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_KEYSTORE_PASSWORD);
			final String confKeyPassword = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SSL_KEY_PASSWORD_ENV_VAR,
				IKafkaConstants.KAFKA_SSL_KEY_PASSWORD);

			props.put(CONF_KAFKA_SSL_TRUSTSTORE_LOCATION, confTruststoreLocation);
			props.put(CONF_KAFKA_SSL_TRUSTSTORE_PASSWORD, confTruststorePassword);
			props.put(CONF_KAFKA_SSL_KEYSTORE_LOCATION, confKeystoreLocation);
			props.put(CONF_KAFKA_SSL_KEYSTORE_PASSWORD, confKeystorePassword);
			props.put(CONF_KAFKA_SSL_KEY_PASSWORD, confKeyPassword);
			props.put(CONF_KAFKA_SSL_ENDPOINT_IDENTIFICATION_ALGORITHM, confEndpointIdentificationAlgorithm);
		}

		if (confSecurityProtocol.contains("SASL")) {
			final String confSaslMechanism = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SASL_MECHANISM_ENV_VAR,
				IKafkaConstants.KAFKA_SASL_MECHANISM);
			final String confSaslJaasConfig = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SASL_JAAS_CONFIG_ENV_VAR,
				IKafkaConstants.KAFKA_SASL_JAAS_CONFIG);
			final String confSaslLoginCallbackHandlerClass = EnvironmentVariablesUtil.getStringEnvironmentVariable(
				KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS_ENV_VAR,
				IKafkaConstants.KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS);

			props.put(CONF_KAFKA_SASL_MECHANISM, confSaslMechanism);
			props.put(CONF_KAFKA_SASL_JAAS_CONFIG, confSaslJaasConfig);
			props.put(CONF_KAFKA_SASL_LOGIN_CALLBACK_HANDLER_CLASS, confSaslLoginCallbackHandlerClass);
		}

		final Consumer<Long, String> consumer = new KafkaConsumer<>(props);
		consumer.subscribe(Collections.singletonList(IKafkaConstants.TOPIC_NAME));
		return consumer;
	}
}
