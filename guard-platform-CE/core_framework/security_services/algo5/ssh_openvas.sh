#!/bin/bash
openvas_id=$(sudo docker ps | grep openvas | cut -d' ' -f 1)
sudo docker exec -it $openvas_id /bin/bash
