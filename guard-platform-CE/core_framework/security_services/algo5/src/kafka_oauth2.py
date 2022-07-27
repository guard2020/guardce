import time
from os import environ,path
from threading import Thread

from kafka.oauth.abstract import AbstractTokenProvider
from oauthlib.oauth2 import BackendApplicationClient
from requests_oauthlib import OAuth2Session

# for oauth debugging
# import logging
# import sys
#  log = logging.getLogger('requests_oauthlib')
#  log.addHandler(logging.StreamHandler(sys.stdout))
#  log.setLevel(logging.DEBUG)

class Oauth2TokenProvider(Thread, AbstractTokenProvider):

    def __init__(self, config):
        super().__init__()
        self.daemon = True

        self.__config = config
        self.__token = self.retrieve_token(first_time=True)

        self.start()

    def retrieve_token(self, first_time=False):
        """
        interact with the OAuth2 token endpoint.

        :return: the OAuth2 token
        """
        token = None if first_time else self.__token
        client = BackendApplicationClient(client_id=self.__config['client_id'],
                                          scope=self.__config['scopes'])
        oauth = OAuth2Session(client=client,
                              token=token,
                              scope=self.__config['scopes'],
                              auto_refresh_url=self.__config['token_uri'])
        return oauth.fetch_token(token_url=self.__config['token_uri'],
                                 client_id=self.__config['client_id'],
                                 client_secret=self.__config['secret'],
                                 verify=self.__config['idp_tls'])

    def token(self):
        """
        the interface specified by the ABC
        :return:  the access token. Note that this will explode if there's no
                  token available.
        """
        return self.__token['access_token']

    def run(self):
        while True:
            print(f'Token will expire in {self.__token["expires_in"]} seconds.')
            time.sleep(self.__token['expires_in'] - 55)  # one minute early
            self.__token = self.retrieve_token()


def remove_prefix(target, prefix):
    return target[len(prefix):]

def get_kafka_config(instance_id=1, dyn_scopes=None):
    kafka_config = {}
    oauth2_config = {}
    oauth2_config_prefix = f'OAUTH2_INSTANCE_{instance_id}_'

    for k,v in environ.items():
        if k.startswith('KAFKA_CONFIG_'):
            kafka_config_key = remove_prefix(k, 'KAFKA_CONFIG_').lower()
            kafka_config[kafka_config_key] = v.strip()
        elif k.startswith(oauth2_config_prefix):
            oauth2_config_key = remove_prefix(k, oauth2_config_prefix).lower()
            oauth2_config[oauth2_config_key] = v.strip()

    if 'client_id' not in oauth2_config or 'secret' not in oauth2_config:
        raise Exception(f"""Credentials for OAuth2 instance {instance_id} were
                        not found. Please define them as configuration
                        parameters.""")
    if dyn_scopes!=None:
        oauth2_config['scopes']=f"{oauth2_config['scopes']} {dyn_scopes}" #Add not-fixed OIDC scopes

    token_provider = Oauth2TokenProvider(oauth2_config)
    kafka_config['sasl_oauth_token_provider'] = token_provider

    return kafka_config
