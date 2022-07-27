#!/bin/bash
#add fact host1
curl -H "Content-type: application/json" -X POST http://127.0.0.1:9000/gfg/publishTopologyChange -d '{ "node": "ITE29102020-2", "modificationType": "ITE29102020-2", "nodeData": "ITE29102020-2" }'