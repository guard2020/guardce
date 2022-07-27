package eu.smartcontroller.guard.demo;

import eu.smartcontroller.guard.demo.controller.KafkaProducerController;
import org.kie.api.KieServices;
import org.kie.api.runtime.KieContainer;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;

@SpringBootApplication
public class SpringBootKafkaAppApplication {

	// env-variables
	static public String kafkaEndpoint;
	static public String kafkaExternalEndpoint;
	static public String topicToPublish;
	static public String topicToListen;

	public static void main(String[] args) {

		SpringApplication.run(SpringBootKafkaAppApplication.class, args);

		// read kafkaExternalEndpoint(IP:port) from the corresponding env-variable
		kafkaExternalEndpoint=System.getenv("GUARD_SERVER");
		if (kafkaExternalEndpoint==null) {
			KafkaProducerController.logger.info("env variable kafkaExternalEndpoint is null!");
			kafkaExternalEndpoint="127.0.0.1:9092";
		}
		KafkaProducerController.logger.info("kafkaExternalEndpoint is set to " + kafkaExternalEndpoint);

		// read kafkaEndpoint(IP:port) from the corresponding env-variable
		kafkaEndpoint=System.getenv("kafkaEndpoint");
		if (kafkaEndpoint==null) {
			KafkaProducerController.logger.info("env variable kafkaEndpoint is null!");
			kafkaEndpoint="10.0.0.7:9092";
		}
		KafkaProducerController.logger.info("kafkaEndpoint is set to " + kafkaEndpoint);

		// read topicTopublish from the corresponding env-variable
		topicToPublish=System.getenv("topicToPublish");
		if (topicToPublish==null) {
			KafkaProducerController.logger.info("env variable topicToPublish is null!");
			topicToPublish="detection-results";
		}
		KafkaProducerController.logger.info("topicToPublish is set to " + topicToPublish);

		// read topicTolisten from the corresponding env-variable
		topicToListen=System.getenv("KAFKA_ALGO_TOPIC");
		if (topicToListen==null) {
			KafkaProducerController.logger.info("env variable topicToListen is null!");
			topicToListen="vuln_scanner";
		}
		KafkaProducerController.logger.info("topicToListen is set to " + topicToListen);

	}

	@Bean
	public KieContainer kieContainer() {
		return KieServices.Factory.get().getKieClasspathContainer();
	}

}
