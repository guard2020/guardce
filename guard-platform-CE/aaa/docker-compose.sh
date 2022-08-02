#!/usr/bin/env bash

compose_files=$(ls ./*/compose.yml)
printf -v compose_flag -- "-f %s " $compose_files
docker-compose -f compose.yml $compose_flag $*
