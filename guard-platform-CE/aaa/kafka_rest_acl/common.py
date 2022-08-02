import requests

http_ok = 200
http_created = 201

def add_common_arguments(arg_parser):
    arg_parser.add_argument('resource_type',
                            type=str,
                            help='Type of the resource to be accessed, e.g., "TOPIC".')
    arg_parser.add_argument('resource_name',
                            type=str,
                            help='Name of the resource to be accessed, e.g., the topic name.')
    arg_parser.add_argument('pattern_type',
                            type=str,
                            help='Type of the pattern being used to identify the resource, e.g., "LITERAL".')
    arg_parser.add_argument('principal',
                            type=str,
                            help='Name of the principal.')
    arg_parser.add_argument('host',
                            type=str,
                            help='Host identifier, address, or pattern.')
    arg_parser.add_argument('operation',
                            type=str,
                            help='Operation to be authorized on the resource, e.g., "DESCRIBE".')
    arg_parser.add_argument('--rest_endpoint_url',
                            type=str,
                            help='REST Endpoint URL to contact. Defaults to "http://localhost:9090".',
                            default='http://localhost:9090')

def get_cluster_id(base_url: str) -> str:
    r = requests.get(f'{base_url}/v3/clusters/')
    assert(r.status_code == http_ok)
    return r.json()['data'][0]['cluster_id']
