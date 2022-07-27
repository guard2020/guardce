FROM guardproject/lcp:api
RUN apt-get update && apt-get install -y jq bash curl wget net-tools procps strace file vim less nano iputils-ping host openssh-client
#RUN apk add --update --no-cache python3 && ln -sf python3 /usr/bin/python && apk add jq bash curl wget net-tools procps strace file
#RUN python3 -m ensurepip
#RUN pip3 install --no-cache --upgrade pip setuptools && pip3 install kafka-python && pip3 install joblib
###RUN apt-get install -y python3-pip python3-joblib python3-kafka
RUN pip install kafka-python joblib
RUN mkdir /opt/guard/agent
COPY simulate_data.py /opt/guard/agent
COPY vdpi_output_13Apr21.log /opt/guard/agent
COPY create_topics_openshift.py /opt/guard
COPY delete_topics_openshift.py /opt/guard
COPY mytopics.txt /opt/guard 
RUN mkdir /opt/guard/lcp/config
COPY LCPConfig.yaml /opt/guard/lcp/config
RUN chmod 666 /opt/guard/lcp/config/
ENTRYPOINT ["python3", "/opt/guard/lcp/main.py"] 
