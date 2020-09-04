FROM php:7.4-fpm

# В официальном образе PHP (от которого мы наследуемся) содержатся исходники всех PHP расширений входящих в состав ядра -
# список расширений - https://www.php.net/manual/ru/extensions.membership.php.
# Образ содержит готовые bash скрипты для сборки и установки расширений:
# - docker-php-ext-configure - передает дополнительные агрументы если они требуются перед сборкой.
# - docker-php-ext-install - устанавливает расширения, причем все зависимости нужно устанавливать вручную с помощью apt-get
# - docker-php-ext-enable - включает расширение. Требуется если расширение установлено через PECL или скачано/собрано вручную
# Расширения PECL
# Некоторые расширения не предоставляются с исходным кодом PHP, но доступны через PECL.
# Чтобы установить расширение PECL, используйте pecl install для его загрузки и компиляции,
# затем используйте docker-php-ext-enable, чтобы включить его.
RUN apt-get update && apt-get install -y \
        cron \
        git \
        # Устанавливаем зависимости для php расширений:
        # for  zip
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
    # Передаем дополнительные параметры перед сборкой расширений. Список параметров есть в документации к расширению.
    && docker-php-ext-configure gd \
            --with-webp \
            --with-jpeg \
            --with-freetype \
    && docker-php-ext-configure intl \
	# Устанавливаем расшиерния
	&& docker-php-ext-install \
	     zip \
	     bcmath \
	     gd \
	     mysqli \
	     pdo_mysql \
	     intl \
	     # pdo_pgsql \
	     # soap \
	# Устанавливаем расшерения через PECL. После установки их нужно включить - docker-php-ext-enable
	# && pecl install redis-5.0.2 xdebug-2.7.2 \
	# && docker-php-ext-enable redis xdebug \
	# Скачиваем composer
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Копируем дэфолтный конфиг
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# if you use cron
COPY cron-file /etc/cron.d/cron-file
COPY run-deamons.sh /run-deamons.sh
ENTRYPOINT /run-deamons.sh
