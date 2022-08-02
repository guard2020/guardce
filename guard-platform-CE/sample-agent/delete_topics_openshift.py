from kafka.admin import KafkaAdminClient, NewTopic


admin_client = KafkaAdminClient(
    bootstrap_servers="kafka-headless.guardce.svc:9092", 
    client_id='testi1'
)

topics = []
with open("mytopics.txt") as file:
    for l in file:
        topics.append(l.strip())
    print(topics)
        
###topics = ['Berlin', 'Milano', 'Sydney', 'Moscow']
    admin_client.delete_topics(topics=topics, timeout_ms=1000)
    print("deleted")
###topic_list  = []
###for listitem in topics:
###    print (listitem)
        
###    topic_list = []
###    topic_list.append(NewTopic(name=listitem, num_partitions=1, replication_factor=3)
###    admin_client.delete_topics(topics=topic_list, timeout_ms=1000)

