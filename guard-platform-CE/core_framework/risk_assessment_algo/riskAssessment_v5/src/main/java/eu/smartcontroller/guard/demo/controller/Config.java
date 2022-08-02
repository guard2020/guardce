package eu.smartcontroller.guard.demo.controller;

import eu.smartcontroller.guard.demo.model.securityPolicies.kafkamessage2.VulnScannerOutput;
import org.apache.kafka.clients.consumer.ConsumerConfig;
import org.apache.kafka.common.serialization.StringDeserializer;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.kafka.annotation.EnableKafka;
import org.springframework.kafka.config.ConcurrentKafkaListenerContainerFactory;
import org.springframework.kafka.core.ConsumerFactory;
import org.springframework.kafka.core.DefaultKafkaConsumerFactory;
import org.springframework.kafka.support.serializer.JsonDeserializer;

import java.util.HashMap;
import java.util.Map;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.kafkaEndpoint;

@EnableKafka
@Configuration
public class Config {

    // Function to establish a connection
    // between Spring application
    // and Kafka server
    @Bean
    public ConsumerFactory<String, VulnScannerOutput> vulnScannerOutputConsumer() {

        // read kafkaEndpoint(IP:port) from the corresponding env-variable
        kafkaEndpoint=System.getenv("kafkaEndpoint");
        if (kafkaEndpoint==null) {
            KafkaProducerController.logger.info("env variable kafkaEndpoint is null!");
            kafkaEndpoint="10.0.0.7:9092";
        }
        KafkaProducerController.logger.info("kafkaEndpoint is set to " + kafkaEndpoint);

        // HashMap to store the configurations
        Map<String, Object> map = new HashMap<>();

        // put the host IP in the map
        map.put(ConsumerConfig.BOOTSTRAP_SERVERS_CONFIG, kafkaEndpoint);

        // put the group ID of consumer in the map
        map.put(ConsumerConfig.GROUP_ID_CONFIG, "id");
        map.put(ConsumerConfig.KEY_DESERIALIZER_CLASS_CONFIG, StringDeserializer.class);
        map.put(ConsumerConfig.VALUE_DESERIALIZER_CLASS_CONFIG, JsonDeserializer.class);

        // return message in JSON format
        JsonDeserializer jsonDeserializer = new JsonDeserializer<>(VulnScannerOutput.class, false);
        jsonDeserializer.addTrustedPackages("*");
        return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), jsonDeserializer);
        //return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), new JsonDeserializer<>(Algo5Output.class));
    }

    @Bean
    public ConcurrentKafkaListenerContainerFactory<String, VulnScannerOutput> vulnScannerOutputListener() {
        ConcurrentKafkaListenerContainerFactory<String, VulnScannerOutput> factory = new ConcurrentKafkaListenerContainerFactory<>();
        factory.setConsumerFactory(vulnScannerOutputConsumer());
        return factory;
    }
}

