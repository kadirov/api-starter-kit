#!/bin/bash
cron -f &
# uncomment if you use workers
#service supervisor start
#supervisorctl reread
#supervisorctl update
#supervisorctl start messenger-consume:*
docker-php-entrypoint php-fpm
