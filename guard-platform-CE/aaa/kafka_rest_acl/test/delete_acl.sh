#!/bin/bash
. .venv/bin/activate
./delete_acl.py TOPIC test LITERAL 'User:*' '*' DESCRIBE
