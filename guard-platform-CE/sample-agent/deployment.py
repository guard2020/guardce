import ast
import json
import logging
import os
import signal
import sys
import time
import warnings

####from about import project, title, version  # noqa: E402
from dynaconf import Dynaconf
from joblib import load
from kafka import KafkaConsumer, KafkaProducer
from rich import print
from rich.console import Console
from rich.logging import RichHandler
from rich.panel import Panel  # noqa: E402


class Data:
    producer = None
    consumer = None
    log = None

    kafka_bootstrap_servers = None
    kafka_topic = None
    kafka_group_id = None
    log_level = None
    log_format = None

    @classmethod
    def init(cls):
        output_filename = "/proc/1/fd/1"
        if (os.path.exists(output_filename)):
            output_file = open(output_filename, "wt")
            cls.console = Console(file=output_file)
        else:
            cls.console = Console(file=sys.stdout)

    @classmethod
    def read(cls):
        data = Dynaconf(settings_files=["config.yaml"])
        cls.kafka_bootstrap_servers = data.kafka.bootstrap_servers
        if isinstance(cls.kafka_bootstrap_servers, list):
            cls.kafka_bootstrap_servers = ",".join(cls.kafka_bootstrap_servers)
        cls.kafka_topic = data.kafka.topic
        cls.kafka_group_id = data.kafka.group_id
        cls.log_level = data.log.level
        cls.log_format = data.log.format

    @classmethod
    def print(cls):
        cls.console.print(f"Kafka Bootstrap Servers: {cls.kafka_bootstrap_servers}")
        cls.console.print(f"Kafka Topic: {cls.kafka_topic}")
        cls.console.print(f"Kafka Group ID: {cls.kafka_group_id}")
        cls.console.print(f"LOG Format: {cls.log_format}")
        cls.console.print(f"LOG Level: {cls.log_level}")

    @classmethod
    def set(cls):
        logging.basicConfig(level=cls.log_level, format=cls.log_format,
                            datefmt="[%X]",
                            handlers=[RichHandler(console=cls.console,
                                                  rich_tracebacks=True,
                                                  omit_repeated_times=False,
                                                  markup=True)]
                            )
        cls.log = logging.getLogger("rich")
        cls.producer = KafkaProducer(bootstrap_servers=cls.kafka_bootstrap_servers)
        cls.consumer = KafkaConsumer(
            cls.kafka_topic,
            group_id=cls.kafka_group_id,
            bootstrap_servers=cls.kafka_bootstrap_servers,
            auto_offset_reset="earliest",
        )


def signal_stop(sig=None, frame=None):
    if os.path.exists('.pidfile'):
        os.remove('.pidfile')
    if os.path.exists(f'.pidfile.{pid}'):
        os.remove(f'.pidfile.{pid}')
    Data.console.rule('Stopping')
    if Data.producer:
        Data.producer.close()
    if Data.consumer:
        Data.consumer.close()
    exit(1)


def signal_restart(sig=None, frame=None):
    Data.console.rule('Restarting')
    Data.log.error("Restarting not yet available")
    # if Data.consumer:
    #     Data.consumer.close()
    # if Data.producer:
    #     Data.producer.close()
    # time.sleep(20)


signal.signal(signal.SIGINT, signal_stop)
signal.signal(signal.SIGTERM, signal_stop)
signal.signal(signal.SIGHUP, signal_restart)

Data.init()

try:
    pid = str(os.getpid())
    with open(".pidfile", "w") as f:
        f.write(pid)
    with open(f".pidfile.{pid}", "w") as f:
        f.write(pid)
    print("ciao")

except Exception as e:
    if Data.log:
        Data.log.exception(e)
    else:
        print(e)
    signal_stop()
