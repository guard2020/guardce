FROM ubuntu:18.04

RUN apt-get clean
RUN apt-get update
RUN apt install -y software-properties-common wget curl
RUN apt-get install -y git gcc

##### Python 3.8 Setup #####

RUN add-apt-repository ppa:deadsnakes/ppa
RUN apt-get update
RUN apt-get install -y python3.8 python3.8-distutils python3.8-dev python3.8-gdbm 

RUN rm /usr/bin/python3
RUN ln -s /usr/bin/python3.8 /usr/bin/python3

RUN curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py
RUN python3.8 get-pip.py

COPY requirements.txt /requirements.txt
RUN pip3 install -r /requirements.txt

##### Python 2.7 Setup #####
RUN rm -rf /var/lib/apt/lists/*
RUN apt-get update
RUN apt-get install -y python2.7 python2.7-dev
RUN ln -s /usr/bin/python2.7 /usr/bin/python
RUN ln -s /usr/bin/python2.7 /usr/bin/python2

RUN curl https://bootstrap.pypa.io/pip/2.7/get-pip.py --output get-pip.py
RUN python2 get-pip.py

##### w3af Setup #####

RUN git clone https://github.com/andresriancho/w3af-api-client.git
WORKDIR w3af-api-client
RUN pip3 install .

WORKDIR /

RUN apt-get -y install build-essential libssl-dev libffi-dev libsqlite3-dev libxslt1-dev libyaml-dev libxml2-dev
RUN pip2 install setuptools
RUN pip2 install pyClamd==0.4.0 PyGithub==1.21.0 GitPython==2.1.15 pybloomfiltermmap==0.3.14 phply==0.9.1 nltk==3.0.1 chardet==3.0.4 tblib==0.2.0 pdfminer==20140328 futures==3.2.0 pyOpenSSL==18.0.0 ndg-httpsclient==0.4.0 pyasn1==0.4.2 lxml==3.4.4 scapy==2.4.0 guess-language==0.2 cluster==1.1.1b3 msgpack==0.5.6 python-ntlm==1.0.1 halberd==0.2.4 darts.util.lru==0.5 Jinja2==2.10 vulndb==0.1.1 markdown==2.6.1 psutil==5.4.8 ds-store==1.1.2 termcolor==1.1.0 mitmproxy==0.13 ruamel.ordereddict==0.4.8 Flask==0.10.1 PyYAML==3.12 tldextract==1.7.2 pebble==4.3.8 acora==2.1 esmre==0.3.1 diff-match-patch==20121119 bravado-core==5.15.0 lz4==1.1.0 vulners==1.3.0 ipaddresses==0.0.2 subprocess32==3.5.4

#RUN apt-get -y install python3-pip python-setuptools

RUN git clone --depth 1 https://github.com/andresriancho/w3af.git

RUN apt install -y nodejs nodejs-dev node-gyp npm
#RUN ln -s /usr/bin/nodejs /usr/bin/node
RUN npm cache clean -f
RUN npm install -g n
RUN n stable

RUN npm install -g retire@2.0.3
RUN npm update -g retire

RUN pip2 install werkzeug==0.16.1 # w3af api mod to fix broken dependency

##### Algo5 setup #####

COPY src/ /opt/algo5

RUN cp -r w3af-api-client/w3af_api_client/ /usr/local/lib/python3.8/dist-packages/

ENTRYPOINT tail -f /dev/null
