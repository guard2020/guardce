import json

from locust import HttpUser
from rich.console import Console

console = Console()

headers = {}
with open('../../.vscode/settings.json') as setting_file:
    settings = json.load(setting_file)
    headers = settings['rest-client.defaultHeaders']
    headers['User-Agent'] = 'vscode-locust'


class BaseUser(HttpUser):
    abstract = True
    filter = {
        'equals': {
            'target': 'partner',
            'expr': 'locust'
        }
    }

    def op(self, method, endpoint, json=None):
        resp = getattr(self.client, method)(endpoint, json=json, headers=headers)
        try:
            resp_data = resp.json()
            if resp.status_code == 406:
                console.print(method, endpoint, resp_data)
            return resp_data
        except Exception:
            return None

    @staticmethod
    def get_ids(data):
        return list(map(lambda x: x.get('id', None), data))
