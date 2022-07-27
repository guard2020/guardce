#!/usr/bin/python3

class Logger:
	_caller_prefix = "LOGGER"
	_verbose = True
	_debug = True
	_warning = True
	_logfile = None

	_OK = '\033[92m'
	_ENDC = '\033[0m'
	_log_levels = {
		"error" : {"prefix" : "[FATAL]", "color" : '\033[91m'},
		"debug" : {"prefix" : "[DEBUG]", "color" : '\033[94m'},
		"warning" : {"prefix" : "[WARNING]", "color" : '\033[93m'}
	}

	@classmethod
	def set_verbose(cls, verbose):
		cls._verbose = verbose

	@classmethod
	def set_logfile(cls, logfile):
		Logger._logfile = logfile
	@classmethod
	def unset_logfile(cls):
		Logger.set_logfile(None)

	@classmethod
	def set_debug_on(cls):
		Logger._debug = True

	@classmethod
	def set_debug_off(cls): # Call if need to turn debug messages off
		Logger._debug = False

	@classmethod
	def set_warning_on(cls):
		Logger._warning = True

	@classmethod
	def set_warning_off(cls): # Call if need to turn warnings off
		Logger._warning = False

	@classmethod
	def spit(cls, msg, **kwargs):
		caller_prefix = "[%s]" % kwargs.pop("caller_prefix", "")
		prefix = ""
		txtcolor = Logger._OK
		for arg, value in kwargs.items():
			prefix = Logger._log_levels.get(arg, {}).get("prefix", "")
			txtcolor = Logger._log_levels.get(arg, {}).get("color", Logger._OK)

		out = "%s%s%s %s%s" % (txtcolor, caller_prefix, prefix, msg, Logger._ENDC)
		if Logger._verbose:
			if (not kwargs.get("debug") and not kwargs.get("warning")) or (kwargs.get("debug") and Logger._debug) or (kwargs.get("warning") and Logger._warning):
				print(out)
		if Logger._logfile:
			with open(Logger._logfile, "a") as wfp:
				wfp.write("%s\n" % out)