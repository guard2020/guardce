import random
import uuid

from faker import Faker
from locust import between, task

from base_user import BaseUser

fake = Faker()
type_examples = {
    'integer': [1, 4, 6],
    'number': [10.1, 2.3, 5.6],
    'time-duration': ['20s', '10min', '1h'],
    'string': ['eth0', '/proc/0', '/etc/default'],
    'choice': ['yes', 'no'],
    'boolean': [True, False],
    'binary': [uuid.uuid4().hex, uuid.uuid4().hex, uuid.uuid4().hex]
}


class User(BaseUser):
    wait_time = between(1, 2.5)

    @task(10)
    def get(self):
        for endpoint in ['/catalog/agent', '/instance/agent']:
            self.op('get', endpoint)
            self.op('get', endpoint, json={
                'select': ['id']
            })
            self.op('get', endpoint, json={
                'select': ['id'],
                'where': self.filter
            })

    @task(5)
    def post(self):
        agent_catalog_id = uuid.uuid4().hex
        action_id = uuid.uuid4().hex
        parameter_id = uuid.uuid4().hex
        parameter_type = random.choice(list(type_examples.keys()))
        resource_id = uuid.uuid4().hex
        action = {
            'id': action_id,
            'config': {
                'cmd': uuid.uuid4().hex,
                'args': [uuid.uuid4().hex, uuid.uuid4().hex],
                'daemon': random.choice([True, False])
            },
            'description': uuid.uuid4().hex
        }
        parameter = {
            'id': parameter_id,
            'type': parameter_type,
            'config': {
                'schema': random.choice(['yaml', 'json', 'properties']),
                'source': uuid.uuid4().hex,
                'path': [uuid.uuid4().hex, uuid.uuid4().hex]
            },
            'list': random.choice([True, False]),
            'description': uuid.uuid4().hex,
            'example': random.choice(type_examples[parameter_type])
        }
        resource = {
            'id': resource_id,
            'config': {'path': [uuid.uuid4().hex, uuid.uuid4().hex]},
            'description': uuid.uuid4().hex
        }
        agent_catalog_json = {
            'id': agent_catalog_id,
            'actions': [action],
            'parameters': [parameter],
            'resources': [resource],
            'partner': 'locust'
        }
        if parameter_type == 'choice':
            agent_catalog_json['parameters'][0]['values'] = type_examples['choice'][0]
        self.op('post', '/catalog/agent', json=agent_catalog_json)

        resp_data_exec_env = self.op('get', '/exec-env', json={
            'select': ['id'],
            'where': self.filter
        })

        if isinstance(resp_data_exec_env, list) and len(resp_data_exec_env) > 0:
            self.op('post', '/instance/agent', json={
                'id': uuid.uuid4().hex,
                'agent_catalog_id': agent_catalog_id,
                'exec_env_id': random.choice(self.get_ids(resp_data_exec_env)),
                'status': random.choice(['started', 'stopped', 'unknown']),
                'operations': [{
                    'actions': [
                        {
                            'id': action_id,
                            'output_format': random.choice(['plain', 'lines', 'json'])
                        }
                    ],
                    'parameters': [
                        {
                            'id': parameter_id,
                            'value': uuid.uuid4().hex
                        }
                    ],
                    'resources': [
                        {
                            'id': resource_id,
                            'content': uuid.uuid4().hex
                        }
                    ]
                }]
            })

    @task(5)
    def put(self):
        for endpoint in ['/catalog/agent', '/instance/agent']:
            resp_data = self.op('get', endpoint, json={
                'select': ['id'],
                'where': self.filter
            })
            if isinstance(resp_data, list) and len(resp_data) > 0:
                data = random.choice(resp_data)
                data['updated'] = True
                self.op('put', endpoint, json=data)

    @task(1)
    def delete(self):
        for endpoint in ['/catalog/agent', '/instance/agent']:
            self.op('delete', endpoint, json={
                'where': self.filter
            })
