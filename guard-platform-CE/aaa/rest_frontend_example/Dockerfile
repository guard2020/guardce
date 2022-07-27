FROM alpine:latest

####
# Install base dependencies
####
RUN apk update \
    && apk add py3-pip

####
# Project files
####
COPY ./* /opt/project/
WORKDIR /opt/project/

####
# Project dependencies
####
RUN pip install -r requirements.txt

# Expose Flask ports
EXPOSE 5000

# Startup
CMD python3 webapp.py
