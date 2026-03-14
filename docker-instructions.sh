# Board Docker commands (run from source-watcher-board/).
# Full workflow and port layout: see ../README.md (Running the board).

# Build and start the board (use --build after Dockerfile changes)
docker compose up -d --build web-server

# Start existing containers
docker compose up -d

# List containers
docker compose ps

# Stop
docker compose down

# List volumes
docker volume ls

# Remove MySQL volume (when using full compose with mysql-server)
docker volume rm source-watcher-board_mysql-data

# Board: http://localhost:8080/
# phpMyAdmin: http://localhost:5000/ (if mysql-server + phpmyadmin are up)

# PHP 8.4 modules (reference)
# docker run -it --rm php:8.4-fpm php -m
# docker run -it --rm php:8.4-apache php -m
