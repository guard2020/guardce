#!/usr/bin/python3

from uuid import uuid4
from random import choice
import socket
import _pickle as cp
import json

""" Return random identifier (uuid4)
"""
def uuid():
	return str(uuid4())

""" Load `filename` pickle
"""
def load(filename):
	with open(filename, "rb") as fp:
		return cp.load(fp)
""" Dump `obj` in `filename` using pickle's protocol 2 in order to be backwards compatible with Python2's cPickle
"""
def dump(obj, filename):
	with open(filename, "wb") as wfp:
		cp.dump(obj, wfp, protocol = 2)

""" Load `filename` JSON file
"""
def jload(filename):
	with open(filename, "r") as fp:
		return json.load(fp)
""" Dump `obj` in `filename` JSON file
"""
def jdump(obj, filename, indent = 2):
	with open(filename, "w") as wfp:
		json.dump(obj, wfp, indent = indent)

def stringify_exception(e):
	try: exception_str = str(e)
	except Exception as e: exception_str = "Could not stringify exception"
	return exception_str


""" Returns a free (unprivileged) port
"""
port_range = range(1024, 65535) # Only need to generate once
def free_port():
	while True:
		port = choice(port_range)
		s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		try:
			s.bind(('localhost', port))
			s.close() # Possible race condition if another process reserves the port before the caller does
			return port
		except (OSError, socket.error) as e:
			if e.errno == 98: # Port is used
				continue
			raise # Raise anything else