#!/usr/bin/env python
import socket
import ssl
import sys

hostname = sys.argv[1]
port = sys.argv[2]
context = ssl.create_default_context(cafile='./truststore.pem')
context.load_cert_chain('./keystore.pem')

with socket.create_connection((hostname, port)) as sock:
    with context.wrap_socket(sock, server_hostname='kafka1') as ssock:
        print(ssock.version())
        ssock.write('test\r\n'.encode())
