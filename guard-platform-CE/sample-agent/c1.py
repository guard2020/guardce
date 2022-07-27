from kafka import KafkaAdminClient
from kafka.admin import NewTopic


topic_name = "retention_test3"
admin = KafkaAdminClient(bootstrap_servers=['guard3.westeurope.cloudapp.azure.com:29092'])

topic = NewTopic(name=topic_name, num_partitions=1, replication_factor=3, topic_configs={'retention.bytes': '1024000000', 'retention.ms':'86400000', 'segment.ms':'86400000' })
response = admin.create_topics([topic])
print(response)
