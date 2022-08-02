FROM docker.elastic.co/logstash/logstash:7.11.0

ENV KAFKA_OAUTH_SERVER_PROP_FILE=./config/oauth-configuration.properties

COPY ./config/*   ./config/
COPY ./kafka.rb ./vendor/bundle/jruby/2.5.0/gems/logstash-integration-kafka-10.7.0-java/lib/logstash/inputs/
COPY ./logstash-integration-kafka_jars.rb ./vendor/bundle/jruby/2.5.0/gems/logstash-integration-kafka-10.7.0-java/lib/
COPY --from=guard/aaa/base_kafka:oauth /opt/kafka/libs/liboauthbearer-1.0.0.jar ./vendor/bundle/jruby/2.5.0/gems/logstash-integration-kafka-10.7.0-java/vendor/jar-dependencies/org/telematics/guard/aa/kafka/liboauthbearer/1.0.0/
COPY ./kafka.jks /var/private/ssl/
CMD ./bin/logstash -f ./config/logstash.conf
