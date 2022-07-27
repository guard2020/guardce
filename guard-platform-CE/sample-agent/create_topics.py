from kafka.admin import KafkaAdminClient, NewTopic



###topic_name = "retention_test3"
admin = KafkaAdminClient(bootstrap_servers=['guard3.westeurope.cloudapp.azure.com:29092'], client_id='test1')

topics = []
with open("mytopics.txt") as file:
    for l in file:
        topics.append(l.strip())

for listitem in topics:
	topic = NewTopic(name=listitem, num_partitions=1, replication_factor=3, topic_configs={'retention.bytes': '1024000000', 'retention.ms':'86400000', 'segment.ms':'86400000' })
	response = admin.create_topics([topic])
	print(response)
