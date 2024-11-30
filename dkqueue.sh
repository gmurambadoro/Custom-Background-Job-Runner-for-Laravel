#!/usr/bin/env bash

source docker.env

docker exec -it "$DC_APP_NAME" php artisan queue:work --queue=high,medium,default

