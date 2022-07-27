#!/usr/bin/python3

import sys
sys.path.append(".")

from Utils.Logger import Logger

from Vulnerability.Orchestrator import Orchestrator

caller_prefix = "SETUP"

if __name__ == "__main__":
	if Orchestrator.check_setup():
		Logger.spit("Setup is OK", caller_prefix = caller_prefix)
	else:
		Logger.spit("Setup not OK", error = True, caller_prefix = caller_prefix)
