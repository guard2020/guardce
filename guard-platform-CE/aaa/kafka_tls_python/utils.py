from os import environ

def removeprefix(s: str, pfx: str) -> str:
    from sys import version_info
    # See: https://docs.python.org/3/library/stdtypes.html#str.removeprefix
    return s.removeprefix(pfx) if version_info.major >= 3 and version_info.minor >= 9 else s[len(pfx):]

def get_env_config():
    config = {}

    for k, v in environ.items():
        if k.startswith('KAFKA_'):
            config_key = removeprefix(k, 'KAFKA_').lower()
            config[config_key] = v.strip()

    return config
