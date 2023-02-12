FROM php:8.2.0-fpm

RUN apt-get update && apt-get install -y \
        cron \
        git \
        libzip-dev \
        zip \
        unzip \
        # - for pdo_pgsql
        # libpq-dev \
        # - for soap
        # libxml2-dev \
        # for GD
        libwebp-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libfreetype6-dev \
        # for intld
        libicu-dev \
    && docker-php-ext-configure gd \
            --with-webp \
            --with-jpeg \
            --with-freetype \
    && docker-php-ext-configure intl \
	&& docker-php-ext-install \
	     zip \
	     bcmath \
	     gd \
	     mysqli \
	     pdo_mysql \
	     intl \
	     # pdo_pgsql \
	     # soap \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# for supervisor & redis
# RUN apt-get install -y supervisor && pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

WORKDIR /var/www/html

COPY cron-file /etc/cron.d/cron-file
COPY run-deamons.sh /run-deamons.sh

ENTRYPOINT /bin/bash /run-deamons.sh
