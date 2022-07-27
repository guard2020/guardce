from kafka.admin import KafkaAdminClient, NewTopic

###topic_name = "retention_test3"
admin = KafkaAdminClient(bootstrap_servers=['guard3.westeurope.cloudapp.azure.com:29092'], client_id='test1')

topics = []
with open("mytopics.txt") as file:
    for l in file:
        topics.append(l.strip())
	print(topics)

    for  itemlist  in  topics:
###topics = ['Berlin', 'Milano', 'Sydney', 'Moscow']
	response = admin.delete_topics(itemlist, timeout_ms=1000)
	print("deleted")
	print(response)
