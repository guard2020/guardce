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
        for endpoint in ['/pipeline']:
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
        self.op('post', '/pipeline', json={
            'id': uuid.uuid4().hex,
            'timestamp': fake.date_time_between(start_date='-1h', end_date='now').isoformat(),
            'partner': 'locust'
        })

    @task(5)
    def put(self):
        for endpoint in ['/pipeline']:
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
        for endpoint in ['/pipeline']:
            self.op('delete', endpoint, json={
                'where': self.filter
            })
