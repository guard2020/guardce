#!/usr/bin/env python3
import requests
import sys
from argparse import ArgumentParser
from common import add_common_arguments, get_cluster_id, http_created

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

r = requests.post(f'{args.rest_endpoint_url}/v3/clusters/{cluster_id}/acls', json=payload)

if (r.status_code != http_created):
    print(r.json())
    sys.exit(1)
else:
    sys.exit(0)
