#!/bin/bash
# This connects to the development database. The user and database name are in docker-compose.yml
docker-compose exec bravendb mysql -h bravendb -P 3306 -u wordpress -pwordpress wordpress
