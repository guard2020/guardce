package org.telematics.guard.aa;

import org.apache.kafka.clients.consumer.Consumer;
import org.apache.kafka.clients.consumer.ConsumerRecords;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class ExampleConsumer {

	private final static Logger log = LoggerFactory.getLogger(ExampleConsumer.class);

	public static void main(String[] args) {
		runConsumer();
	}

	static void runConsumer() {
		Consumer<Long, String> consumer = ConsumerCreator.createConsumer();

		int noMessageToFetch = 0;

		while (true) {
			final ConsumerRecords<Long, String> consumerRecords = consumer.poll(1000);
			if (consumerRecords.count() == 0) {
				noMessageToFetch++;
				if (noMessageToFetch > IKafkaConstants.MAX_NO_MESSAGE_FOUND_COUNT)
					break;
				else
					continue;
			}

			consumerRecords.forEach(record -> {
				log.info("Consumed value: {} from Record Key {} | Partition {} | Offset {}",
						record.value(), record.key(), record.partition(), record.offset());
			});
			consumer.commitAsync();
		}
		consumer.close();
	}
}
