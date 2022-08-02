#!/usr/bin/python3

import warnings
warnings.filterwarnings("ignore")

import sys
sys.path.append(".")

from argparse import ArgumentParser

from GUARDUtils.CBM import CBM
from Vulnerability.Orchestrator import Orchestrator
from Utils.Misc import uuid, jdump, stringify_exception
from Utils.Logger import Logger

import json, os
from time import strftime,sleep

from kafka import KafkaConsumer
from kafka_oauth2 import get_kafka_config

caller_prefix = "LAUNCHER"
parser = ArgumentParser()
def get_args():
	parser.add_argument("-i", "--internal", action = "store", dest = "internal", help = "Configuration file for internal attacker (local agents)")
	parser.add_argument("-e", "--external", action = "store", dest = "external", help = "Configuration file for external attacker (core framework)")
	parser.add_argument("-c", "--cleanup", action = "store_true", dest = "cleanup", help = "Cleanup scanners' environments")
	parser.add_argument("-s", "--status", action = "store_true", dest = "status", help = "Check setup status")
	return parser.parse_args()

def read_from_kafka(task_name):
	consumer = KafkaConsumer("vuln_scanner", value_deserializer = lambda v: json.loads(v.decode()), **get_kafka_config())
	while not consumer.bootstrap_connected():
		print('Waiting for Kafka connection...')
		sleep(1)

	consumer.topics()
	consumer.seek_to_beginning()

	Logger.spit("Waiting results from kafka (local agent)...", caller_prefix = caller_prefix)
	results = None
	while True:
		record = consumer.poll()
		if not record: continue # No results yet
		results = list(record.values())[0][0].value
		if results.get("task_name", None) == task_name: # Got our IDs
			break
		results = None
	return results

if __name__ == "__main__":
	try:
		abs_path = os.path.dirname(os.path.abspath(__file__))
		logs_dir = os.path.join(abs_path, "logs")

		args = get_args()
		internal_config_file = args.internal
		external_config_file = args.external
		cleanup = args.cleanup
		status = args.status
		if status: # Load predefined dummy configuration
			internal_config_file = "input/check_setup.json"
			external_config_file = "input/check_setup.json"

		Logger.set_logfile(os.path.join(logs_dir, "tmp.log"))

		agent_env_id = "vuln-scanner@forthvm"
		if cleanup:
			res = CBM.send_action(agent_env_id, "clean")
			if not res[0]:
				Logger.spit("Agent init failed. Aborting...", error = True, caller_prefix = caller_prefix)
				print(res)
			Orchestrator.cleanup()
			exit(0)

		results = {}
		task_name = uuid()
		if internal_config_file:
			with open(internal_config_file, "r") as fp:
				internal_config = json.load(fp)
			#hosts_param = json.dumps(internal_config["hosts"])
			hosts_param = internal_config["hosts"]


			params = [
				{
					"data" : {
						"path" : ["hosts"],
						"schema" : "json",
						"value" : hosts_param
					},
					"id" : "hosts",
					"timestamp" : strftime("%Y-%m-%dT%H:%M:%S"),
					"value" : hosts_param
				},
				{
					"data" : {
						"path" : ["task_name"],
						"schema" : "json",
						"value" : task_name
					},
					"id" : "task_name",
					"timestamp" : strftime("%Y-%m-%dT%H:%M:%S"),
					"value" : task_name
				}
			]

			Logger.spit("Setting scanning configuration and starting agent...", caller_prefix = caller_prefix)
			res = CBM.send_action(agent_env_id, "start", params)
			if not res[0]:
				Logger.spit("Agent init failed. Aborting...", error = True, caller_prefix = caller_prefix)
				print(res)
				print(res[1].text)
				exit(2)
			Logger.spit("Agent started", caller_prefix = caller_prefix)

		if external_config_file:
			with open(external_config_file, "r") as fp:
				external_config = json.load(fp)
			Logger.set_logfile(os.path.join(logs_dir, external_config["task_name"]+".log"))
			external_config["task_id"] = external_config.get("task_id", uuid())
			Logger.spit("Initiating external scanning...", caller_prefix = caller_prefix)
			orchestrator = Orchestrator(external_config)
			try:
				orchestrator.kickstart()
				return_data = orchestrator.get_reports()
				if status: Logger.spit("Setup OK", caller_prefix = caller_prefix)
				results["external"] = return_data
				# Logger.spit(json.dumps(return_data, indent = 2), caller_prefix = caller_prefix) # tmp debug
			except Exception as e:
				Logger.spit(stringify_exception(e), warning = True, caller_prefix = caller_prefix)
				if status: Logger.spit("Setup not OK", error = True, caller_prefix = caller_prefix)
				Orchestrator.cleanup() # If anything goes wrong, clean up after ourselves

		if internal_config_file:
			results["internal"] = read_from_kafka(task_name)

		jdump(results, os.path.join(logs_dir, "%s.log" % task_name))


	except Exception as ex:
		Logger.spit("EXCEPTION: %s" % stringify_exception(ex), warning = True, caller_prefix = caller_prefix)
		raise
