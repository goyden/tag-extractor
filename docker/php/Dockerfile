FROM php:7.3-fpm
COPY custom.ini $PHP_INI_DIR/conf.d/
ENV PATH $PATH:/var/www/html/vendor/bin
RUN apt-get update \
    # Composer
    && apt-get install -y --no-install-recommends unzip \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    # PostgreSQL PDO
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    # XDebug
    && pecl install xdebug-2.8.0 \
    && docker-php-ext-enable xdebug \
    # php-amqplib
    && docker-php-ext-install bcmath sockets \
    # Cleanning after apt-get
    && apt-get -y autoremove \
    && apt-get clean \
    && apt-get purge \
    && rm -rf /var/lib/apt/lists/*

USER 1000:1000
