#!/usr/bin/env python3
import requests
from common import get_cluster_id, http_ok

base_url = 'http://localhost:9090'

cluster_id = get_cluster_id(base_url)

# get list of ACLs
r = requests.get(f'{base_url}/v3/clusters/{cluster_id}/acls')
assert(r.status_code == http_ok)

print(r.json()['data'])
