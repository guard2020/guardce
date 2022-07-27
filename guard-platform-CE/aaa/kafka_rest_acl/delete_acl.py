#!/usr/bin/env python3
import requests
from argparse import ArgumentParser
from common import add_common_arguments, get_cluster_id, http_ok

ap = ArgumentParser(description='Add new ACL through Kafka REST interface.')
add_common_arguments(ap)
args = ap.parse_args()

payload = {
    'resource_type': args.resource_type,
    'resource_name': args.resource_name,
    'pattern_type': args.pattern_type,
    'principal': args.principal,
    'host': args.host,
    'operation': args.operation,
    'permission': 'ALLOW'
}

cluster_id = get_cluster_id(args.rest_endpoint_url)

r = requests.delete(f'{args.rest_endpoint_url}/v3/clusters/{cluster_id}/acls', json=payload)

print(r.json())
assert(r.status_code == http_ok)
