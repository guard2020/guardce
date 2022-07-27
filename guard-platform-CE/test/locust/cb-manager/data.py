import random
import uuid

from faker import Faker
from locust import between, task

from base_user import BaseUser

fake = Faker()


class User(BaseUser):
    wait_time = between(1, 2.5)

    @task(10)
    def get(self):
        for endpoint in ['/data']:
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
        ebpf = random.choice([True, False])
        if ebpf:
            endpoint = 'ebpf-program'
            field = 'ebpf_program'
        else:
            endpoint = 'agent'
            field = 'agent'
        resp_instance = self.op('get', f'/instance/{endpoint}', json={
            'select': ['id']
        })
        if isinstance(resp_instance, list) and len(resp_instance) > 0:
            self.op('post', '/data', json={
                'id': uuid.uuid4().hex,
                field: random.choice(self.get_ids(resp_instance)),
                'timestamp_event': fake.date_time_between(start_date='-1h', end_date='now').isoformat(),
                'timestamp_agent': fake.date_time_between(start_date='-59min', end_date='now').isoformat(),
                'partner': 'locust'
            })

    @task(5)
    def put(self):
        for endpoint in ['/data']:
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
        for endpoint in ['/data']:
            self.op('delete', endpoint, json={
                'where': self.filter
            })
