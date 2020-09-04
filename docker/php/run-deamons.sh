#!/bin/bash
cron -f &
docker-php-entrypoint php-fpm
