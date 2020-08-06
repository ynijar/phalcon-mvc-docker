# phalcon-mvc-docker
============================

Simple MVC Phalcon 4 with Docker

Common config
-------------------
    config-example.php -> config.php

Start docker
-------------------
    docker-compose up -d --build

Exec container
-------------------
    docker exec -it {CONTAINER_NAME} /bin/bash

Run composer
-------------------
    composer install

Run migrate
-------------------
    vendor/bin/phinx migrate

Open project
-------------------
    http://localhost:8099
