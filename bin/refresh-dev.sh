#!/bin/bash

set -ex

CONTAINER_USER="sail"
COMPOSE_CMD="/usr/local/bin/docker-compose -f docker-compose.cloud.yml"
EXEC_CMD="$COMPOSE_CMD exec -T --user $CONTAINER_USER app"

$EXEC_CMD ./artisan down --render="maintenance"
$EXEC_CMD ./artisan migrate:fresh --seed --force
$EXEC_CMD ./artisan up
