# The --build flag builds images before starting containers. It’s essential to use this flag because without it the changes that we made in php.Dockerfile won’t take effect.
docker-compose up -d --build web-server

# Turn the container up
docker-compose up -d

# List the containers running
docker-compose ps

# Turn the container down
docker-compose down

# List the volumes
docker volume ls

# Delete the MySQL volume
docker volume rm lamp_mysql-data

# http://localhost:5000/index.php
# http://localhost:8181/index.php

# List modules enabled by default on PHP 7.2, PHP 7.3 and PHP 7.4 Docker containers:

docker run -it --rm php:7.2-fpm php -m
docker run -it --rm php:7.3-fpm php -m
docker run -it --rm php:7.4-fpm php -m

docker run -it --rm php:7.2-apache php -m
docker run -it --rm php:7.3-apache php -m
docker run -it --rm php:7.4-apache php -m
