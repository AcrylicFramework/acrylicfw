#!/bin/sh
docker compose exec acrylic php "$@"
return $?