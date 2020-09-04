FROM php:7.4-fpm

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

WORKDIR /var/www/html

COPY cron-file /etc/cron.d/cron-file
COPY run-deamons.sh /run-deamons.sh
ENTRYPOINT /run-deamons.sh
