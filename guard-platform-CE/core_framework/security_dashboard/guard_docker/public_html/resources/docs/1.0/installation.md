# Installation

---

- [Setting environment variables](#env)
- [Docker Installation](#docker)
- [Database migration](#database-migration)
- [Dashboard](#dashboard)


<a name="env"></a>
## Setting environment variables

To be able to install, configure and use the dashboard you will first need to set up the correct environment variables
in the .env file located inside the /public_html folder. 

In the .env file you will need to update the variables to communicate with the different components that the Dashboard communicates with:
- *CB_API* (context broker manager endpoint)
- *SC_API* (security controller endpoint)
- *ELASTICSEARCH_URL* (elasticsearch endpoint)
- *KIBANA_URL* (kibana endpoint)

Optionally, some variables may be changed according to your needs. The *APP_ENV* can be changed for example to "production"
if the Dashboard is being deployed on a production environment. And the *APP_DEBUG* variable can be set to **TRUE** or **FALSE**
if you want to debugging to be enabled or disabled.

Finally, variables related to the database might need to be changed, if you also change the database configuration in the docker-compose file.   


<a name="docker"></a>
## Docker Installation


####Pre settings
By default, you will be able to build the image and start the docker container without having to change any configuration on the _Dockerfile_
or the _docker-compose.yml_ file. However, you might want to change, for example, the ports in which it is deployed or change the database credentials.

If this is done, you might need to apply the changes also to the _.env_ file (database credentials) and if the guard-dashboard ports are changed in the _docker-compose.yml_
file, then the ports might need to be updated in the _vhost.conf_ inside the /public_html directory and also in the _Dockerfile_ the newly added port will need to be exposed.

<br>

#### Building image
To build the image you will need to run the command **docker-compose build**. The build of the image will take some time, 
as all dependencies, libraries and resources will be installed and copied to the image.

        docker-compose build
<br>

#### Starting Dashboard's docker container
After the image was successfully built, you can create the container and start the Dashboard.

        docker-compose up

To start the container in detach mode (run container in the background) you can use the flag -d

        docker-compose up -d  


<a name="database-migration"></a>
## Database migration

After the container is running, you will notice that when accessing the dashboard you will be directed to the login page.
The two default users are:

- Admin
    - Email: admin@mindsandsparks.org
    - Password: m&s2021
    
- Security pipeline officer
    - Email: pipeline.operator@mindsandsparks.org
    - Password: operator2021m&s

<br>

**Note**: If this credentials do not work, it might be that the database is empty. If that is the case, to solve follow the next steps:
1. Access the container and substitute CONTAINER_NAME with the name of your dashboard container (default: guard-dashboard)

        docker exec -u 0 -it CONTAINER_NAME bash

2. Re-create the database tables with the command

        php artisan migrate:fresh
        
3. Seed the database with the default users and roles.

        php artisan db:seed
        
4. Exit the container and attempt to log in again with the same users.

<a name="dashboard"></a>
##Dashboard

Now you should be ready to access and use the Dashboard. Note, the Dashboard uses the Context Broker Manager as its central data database.
This means, if the connection to the Context Broker Manager is not working, the Dashboard will create errors while logging in. 
If the Context Broker Manager is empty, most components will not be working properly.
