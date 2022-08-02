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
        for endpoint in ['/catalog/algorithm', '/instance/algorithm']:
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
        algorithm_catalog_id = uuid.uuid4().hex
        parameter_id = uuid.uuid4().hex
        parameter_type = random.choice(list(type_examples.keys()))
        algorithm_catalog_json = {
            'id': algorithm_catalog_id,
            'description': uuid.uuid4().hex,
            'parameters': [
                {
                    'id': parameter_id,
                    'type': parameter_type,
                    'list': random.choice([True, False]),
                    'description': uuid.uuid4().hex,
                    'example': random.choice(type_examples[parameter_type])
                }
            ],
            'partner': 'locust'
        }
        if parameter_type == 'choice':
            algorithm_catalog_json['parameters'][0]['values'] = type_examples['choice'][0]
        self.op('post', '/catalog/algorithm', json=algorithm_catalog_json)

        resp_data_exec_env = self.op('get', '/exec-env', json={
            'select': ['id'],
            'where': self.filter
        })

        if isinstance(resp_data_exec_env, list) and len(resp_data_exec_env) > 0:
            self.op('post', '/instance/algorithm', json={
                'id': uuid.uuid4().hex,
                'algorithm_catalog_id': algorithm_catalog_id,
                'operations': [{
                    'parameters': [
                        {
                            'id': parameter_id,
                            'value': uuid.uuid4().hex
                        }
                    ]
                }],
                'descriptions': uuid.uuid4().hex
            })

    @task(5)
    def put(self):
        for endpoint in ['/catalog/algorithm', '/instance/algorithm']:
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
        for endpoint in ['/catalog/algorithm', '/instance/algorithm']:
            self.op('delete', endpoint, json={
                'where': self.filter
            })
