from os import listdir
import sys, re, csv, ast, json, time, os
from kafka import KafkaConsumer, KafkaProducer
from joblib import dump, load

file = open('/opt/guard/agent/vdpi_output_13Apr21.log', 'r')
lines = file.read().splitlines()
file.close()


producer = KafkaProducer(bootstrap_servers=os.environ.get('KAFKA_BOOTSTRAP_SERVERS'))

counter=0
for line in lines:
#    print(line)
    producer.send(os.environ.get('KAFKA_ALGO_TOPIC'), json.dumps(json.loads(line)).encode('utf-8'))
    time.sleep(0.1)
