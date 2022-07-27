# CB-Manager (Context Broker Manager)

[![License](https://img.shields.io/github/license/guard-project/cb-manager)](https://github.com/guard-project/cb-manager/blob/master/LICENSE)
[![Code size](https://img.shields.io/github/languages/code-size/guard-project/cb-manager?color=red&logo=github)](https://github.com/guard-project/cb-manager)
[![Repository Size](https://img.shields.io/github/repo-size/guard-project/cb-manager?color=red&logo=github)](https://github.com/guard-project/cb-manager)
[![Release](https://img.shields.io/github/v/tag/guard-project/cb-manager?label=release&logo=github)](https://github.com/guard-project/cb-manager/releases)
[![Docker image](https://img.shields.io/docker/image-size/guardproject/cb-manager?label=image&logo=docker)](https://hub.docker.com/repository/docker/guardproject/cb-manager)
[![Docs](https://readthedocs.org/projects/guard-cb-manager/badge/?version=latest)](https://guard-cb-manager.readthedocs.io)

## Contents

- [CB-Manager (Context Broker Manager)](#cb-manager-context-broker-manager)
  - [Contents](#contents)
  - [Installation Steps](#installation-steps)
    - [Setup](#setup)
    - [Requirements](#requirements)
    - [Initialization](#initialization)
    - [Start](#start)
    - [Stop](#stop)
    - [Health](#health)
  - [Docker image](#docker-image)
    - [Build](#build)
    - [Run](#run)

The source code is available in the [src](github.com/guard-project/cb-manager) directory as git sub-module.

## Installation Steps

### Setup

The variables are defined in [scripts/vars](scripts/vars) and in the .env file depending on the chosen version (variable `VERSION` in the table).

Name              | Default value           | Meaning
------------------|-------------------------|--------
COMPONENT         | cb-manager              | Component name
VERSION           | master                  | Component version
PROJECT           | guard                   | Project name
INSTALLATION_PATH | /opt/`$COMPONENT`       | Destination path where the software will be installed
TMP_PATH          | /tmp                    | Temporary dictionary path
PIDFILE           | `$TMP`/`$COMPONENT`.pid | File path where the PID of the current execution is stored

### Requirements

Enter into the `scripts` directory.

```console
$ cd scripts
```

### Initialization

```console
$ bash ./init.sh
```

### Start

```console
$ bash ./start.sh
```

### Stop

```console
$ bash ./stop.sh
```

### Health

Check if the software is running or not.

```console
$ bash ./health.sh
```

## Docker image

[Dockerfile](Dockerfile) is used to build the `docker` image with CI in the [https://hub.docker.com/repository/docker/guardproject/cb-manager](https://hub.docker.com/repository/docker/guardproject/cb-manager).

### Build

You can build the image with tag guardproject/cb-manager:`$VERSION`.
`$VERSION` is the specific version to build the image.

```console
$ docker build . -t guardproject/cb-manager:$VERSION
```

Example:

```console
$ VERSION=master
$ docker build . -t guardproject/cb-manager:$VERSION
```

### Run

In addition, it is possible to run the image in a container specifying the environment variable using the specific .env file for the chosen version.

```console
$ docker run --env-file settings/$VERSION/.env --name cb-manager.$VERSION guardproject/cb-manager:$VERSION
```

Example:

```console
$ VERSION=master
$ docker run --env-file settings/$VERSION/.env --name cb-manager.$VERSION guardproject/cb-manager:$VERSION
```
