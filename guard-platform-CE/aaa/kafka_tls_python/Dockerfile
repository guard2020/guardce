FROM docker.io/python:slim

WORKDIR /opt

COPY ./requirements.txt ./
RUN pip install -r requirements.txt

COPY ./*.pem ./
COPY ./*.py ./
