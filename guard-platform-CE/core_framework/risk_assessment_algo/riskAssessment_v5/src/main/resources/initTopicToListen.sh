#!/bin/sh

sed -i "/topicName=/c\topicName=${topicToListen}" src/main/resources/application.properties
