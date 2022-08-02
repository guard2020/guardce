package eu.smartcontroller.guard.demo.controller;

import com.google.gson.JsonObject;
import eu.smartcontroller.guard.demo.cnitml.CnitMLOutput;
import eu.smartcontroller.guard.demo.model.aminer.AMinerOutput;
import org.apache.kafka.clients.consumer.ConsumerConfig;
import org.apache.kafka.common.protocol.types.Field;
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
    public ConsumerFactory<String, AMinerOutput> aMinerOutputConsumer() {

        // read kafkaEndpoint(IP:port) from the corresponding env-variable
        kafkaEndpoint=System.getenv("GUARD_SERVER_ADDRESS");
        if (kafkaEndpoint==null) {
            KafkaProducerController.logger.info("env variable kafkaEndpoint is null!");
            //kafkaEndpoint="127.0.0.1:9092";
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
        JsonDeserializer jsonDeserializer = new JsonDeserializer<>(AMinerOutput.class, false);
        jsonDeserializer.addTrustedPackages("*");
        return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), jsonDeserializer);
        //return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), new JsonDeserializer<>(AMinerOutput.class));
    }

    @Bean
    public ConcurrentKafkaListenerContainerFactory<String, AMinerOutput> aMinerOutputListener() {
        ConcurrentKafkaListenerContainerFactory<String, AMinerOutput> factory = new ConcurrentKafkaListenerContainerFactory<>();
        factory.setConsumerFactory(aMinerOutputConsumer());
        factory.setRecordFilterStrategy(record -> record.value().contains("EntropyDetector"));
        return factory;
    }

    // Function to establish a connection
    // between Spring application
    // and Kafka server
    @Bean
    public ConsumerFactory<String, CnitMLOutput> cnitMlOutputConsumer() {

        // read kafkaEndpoint(IP:port) from the corresponding env-variable
        kafkaEndpoint=System.getenv("GUARD_SERVER_ADDRESS");
        if (kafkaEndpoint==null) {
            KafkaProducerController.logger.info("env variable kafkaEndpoint is null!");
            //kafkaEndpoint="127.0.0.1:9092";
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
        JsonDeserializer jsonDeserializer = new JsonDeserializer<>(CnitMLOutput.class, false);
        jsonDeserializer.addTrustedPackages("*");
        return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), jsonDeserializer);
        //return new DefaultKafkaConsumerFactory<>(map, new StringDeserializer(), new JsonDeserializer<>(Object.class));
    }

    @Bean
    public ConcurrentKafkaListenerContainerFactory<String, CnitMLOutput> cnitMlOutputListener() {
        ConcurrentKafkaListenerContainerFactory<String, CnitMLOutput> factory = new ConcurrentKafkaListenerContainerFactory<>();
        factory.setConsumerFactory(cnitMlOutputConsumer());
        factory.setRecordFilterStrategy(record -> record.value().contains("ALGO112_v3"));
        return factory;
    }
}

