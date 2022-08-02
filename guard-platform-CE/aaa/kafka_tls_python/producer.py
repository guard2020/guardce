#!/usr/bin/env python
import ssl
import time

from kafka import KafkaProducer
from utils import get_env_config

def main():
    ssl_ctx = None
    cfg = get_env_config()

    if 'security_protocol' in cfg and 'ssl' in cfg['security_protocol'].lower():
        cafile = cfg['ssl_cafile'] if 'ssl_cafile' in cfg else None
        certfile = cfg['ssl_certfile'] if 'ssl_certfile' in cfg else None

        ssl_ctx = ssl.create_default_context(cafile=cafile)
        ssl_ctx.load_cert_chain(certfile)

    producer = KafkaProducer(**get_env_config(), ssl_context=ssl_ctx)

    while not producer.bootstrap_connected():
        print('Waiting for connection...')
        time.sleep(1)

    for i in range(5):
        print(f'==> Hello world! - {i}')
        producer.send('test', value=f'Hello world! - {i}'.encode())


if __name__ == '__main__':
    main()
