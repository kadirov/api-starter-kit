FROM alpine:3.20

RUN apk add --no-cache \
    bash tzdata ca-certificates curl coreutils findutils jq \
    tar gzip \
    mariadb-client \
    postgresql-client

COPY backup.sh /usr/local/bin/backup.sh
RUN chmod +x /usr/local/bin/backup.sh

CMD ["crond", "-f", "-l", "5"]
