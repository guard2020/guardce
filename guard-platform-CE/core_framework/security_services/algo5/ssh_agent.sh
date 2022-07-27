#!/bin/bash
algo5_id=$(sudo docker ps | grep algo5 | cut -d' ' -f 1)
sudo docker exec -it $algo5_id /bin/bash
