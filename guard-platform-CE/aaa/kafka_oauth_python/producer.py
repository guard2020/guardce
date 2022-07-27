import time

from kafka import KafkaProducer

from kafka_oauth2 import get_kafka_config


def main():
    producer = KafkaProducer(**get_kafka_config())

    while not producer.bootstrap_connected():
        print('Waiting for connection...')
        time.sleep(1)

    for i in range(100):
        print(f'==> Hello world! - {i}', flush=True)
        producer.send('test', value=f'Hello world! - {i}'.encode())
        time.sleep(60 * 5)


if __name__ == '__main__':
    main()
