#!/bin/bash
. .venv/bin/activate
./create_acl.py TOPIC test LITERAL "User:ANONYMOUS,UNAUTHENTICATED,SSL" '*' DESCRIBE
