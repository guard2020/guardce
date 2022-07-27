##GUARD Security Dashboard

To deploy the docker container please run the command indicated in the following steps:

- Make sure you are inside the folder *guard_docker*.
- Run *docker-compose up* with the flag *--build*. The detach flag *-d* is optional.

-       docker-compose up --build -d


- To stop the container:

-       docker-compose down

- The flag *--build* is optional after the container has been deployed for the first time. To start without building just run compose up

-       docker-compose up -d
