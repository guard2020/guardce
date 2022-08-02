package eu.smartcontroller.guard.demo.controller;

import eu.smartcontroller.guard.demo.model.TopologyChange;
import org.apache.kafka.clients.producer.ProducerConfig;
import org.apache.kafka.common.serialization.StringSerializer;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.kafka.core.*;
import org.springframework.kafka.support.serializer.JsonSerializer;

import java.util.HashMap;
import java.util.Map;

import static eu.smartcontroller.guard.demo.SpringBootKafkaAppApplication.kafkaEndpoint;

@Configuration
public class TopologyChangeConfig {

    @Bean
    public ProducerFactory<String, TopologyChange> producerFactory() {
        // HashMap to store the configurations
        Map<String, Object> config = new HashMap<>();
        config.put(ProducerConfig.BOOTSTRAP_SERVERS_CONFIG, kafkaEndpoint);
        //Config.put(ProducerConfig.BOOTSTRAP_SERVERS_CONFIG, "127.0.0.1:9092");
        config.put(ProducerConfig.KEY_SERIALIZER_CLASS_CONFIG, StringSerializer.class);
        config.put(ProducerConfig.VALUE_SERIALIZER_CLASS_CONFIG, JsonSerializer.class);

        return new DefaultKafkaProducerFactory<>(config);
    }

    @Bean
    public KafkaTemplate<String, TopologyChange> kafkaTemplate() {
        return new KafkaTemplate<>(producerFactory());
    }
}
