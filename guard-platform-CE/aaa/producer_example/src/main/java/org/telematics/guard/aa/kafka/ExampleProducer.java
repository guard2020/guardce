package org.telematics.guard.aa;

import org.apache.kafka.clients.producer.Producer;
import org.apache.kafka.clients.producer.ProducerRecord;
import org.apache.kafka.clients.producer.RecordMetadata;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.concurrent.ExecutionException;

public class ExampleProducer {
	private final static Logger log = LoggerFactory.getLogger(ExampleProducer.class);

	public static void main(String[] args) {
		runProducer();
	}

	static void runProducer() {
		Producer<Long, String> producer = ProducerCreator.createProducer();

		for (int i = 0; i < IKafkaConstants.MESSAGE_COUNT; i++) {
			final ProducerRecord<Long, String> record = new ProducerRecord<>(IKafkaConstants.TOPIC_NAME,
				"Record " + i);

			try {
				RecordMetadata metadata = producer.send(record).get();

				log.info("Published Record: Key {} | Partition {} | Offset {}",
					i, metadata.partition(), metadata.offset());
			} catch (ExecutionException | InterruptedException e) {
				log.error("Cannot publish Record", e);
			}
		}
	}
}
