#!/usr/bin/python3

import os

from requests import get, post, put, delete
from json import load, loads, dumps

from datetime import datetime as dt
from time import strftime

class CBM(object):
	caller_prefix = "CBM"

	_abs_path = os.path.dirname(os.path.abspath(__file__))
	_cbm_config_file = os.path.join(_abs_path, "config/cbm_config.json")

	with open(_cbm_config_file, "r") as fp:
		_config = load(fp)

	_scheme = _config["cb-manager"]["scheme"]
	_host = _config["cb-manager"]["host"]
	_port = _config["cb-manager"]["port"]

	_headers = {"Content-type" : "application/json", "Authorization" : "GUARD eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2MTg4MjI3OTIsImV4cCI6IjE2NTAzNTg3OTIiLCJuYmYiOiIxNjE4NzM2MzkyIn0.-bU-OLHRPWeyGwVPD_DnO67dbfwm3NjPK52yIlhQscc"}

	@classmethod
	def _craft_cbm_url(cls, endpoint, *args):
		url = "%s://%s:%s/%s" % (CBM._scheme, CBM._host, CBM._port, endpoint)
		for arg in args: url += "/%s" % arg if arg else ""
		return url

	@classmethod
	def get_exec_envs(cls):
		return loads(get(CBM._craft_cbm_url("exec-env"), headers = CBM._headers).text)

	@classmethod
	def get_exec_env(cls, exec_env_id):
		return loads(get(CBM._craft_cbm_url("exec-env", exec_env_id), headers = CBM._headers).text)

	@classmethod
	def get_catalog_agents(cls, agent_id = None):
		return loads(get(CBM._craft_cbm_url("catalog/agent", agent_id), headers = CBM._headers).text)
	@classmethod
	def get_catalog_agent(cls, agent_id):
		return CBM.get_catalog_agents(agent_id = agent_id)

	@classmethod
	def get_instance_agents(cls):
		return loads(get(CBM._craft_cbm_url("instance/agent"), headers = CBM._headers).text)

	@classmethod
	def get_instance_agent(cls, agent_id):
		return loads(get(CBM._craft_cbm_url("instance/agent", agent_id), headers = CBM._headers).text)

	''' Get all supported agents for the specific exec-env
	'''
	@classmethod
	def get_exec_env_agents(cls, exec_env_id):
		instance_agents = CBM.get_instance_agents()
		exec_env_agents = []
		for agent in instance_agents:
			if agent["exec_env_id"] != exec_env_id: continue
			exec_env_agents.append(agent)
		return exec_env_agents

	''' Get all exec-envs that have the given agent id
	'''
	@classmethod
	def get_exec_envs_with_agent(cls, agent_id):
		exec_envs = CBM.get_exec_envs() # Collect all environments
		instance_agents = CBM.get_instance_agents() # Get all instance agents
		
		exec_envs_with_agent = set([ia["exec_env_id"] for ia in instance_agents if ia["agent_catalog_id"] == agent_id]) # Gather exec_env_ids based on given agent_id
		return [ev for ev in exec_envs if ev["id"] in exec_envs_with_agent] # Return full exec_env objects

	@classmethod
	def set_parameters(cls, instance_id, parameters):
		data = dumps({
			"operations" : [{"parameters" : parameters}],
			"timestamp" : strftime("%Y-%m-%dT%H:%M:%s")
		})

		res = put(CBM._craft_cbm_url("instance/agent/%s" % instance_id), data = data, headers = CBM._headers)
		if res.status_code != 200: return (False, res)
		else: return (True, res)

	@classmethod
	def send_action(cls, instance_id, action_id, parameters):
		data = dumps({
			"operations" : [
				{"parameters" : parameters},
				{"actions" : [{"id" : action_id, "timestamp" : strftime("%Y-%m-%dT%H:%M:%S")}]},
			],
		})

		res = put(CBM._craft_cbm_url("instance/agent/%s" % instance_id), data = data, headers = CBM._headers)
		if res.status_code != 200: return (False, res)
		else: return (True, res)


if __name__ == "__main__":
	#res = CBM.get_catalog_agent("vuln-scanner")
	#res = CBM.get_instance_agent("vuln-scanner@forthvm")
	#res = CBM.get_instance_agents()
	#res = CBM.get_exec_env("forthvm")
	res = CBM.get_catalog_agents()
	print(dumps(res, indent = 2))
