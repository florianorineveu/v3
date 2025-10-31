FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache \
    git \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    bash \
    curl

RUN docker-php-ext-install \
    pdo pdo_pgsql \
    intl \
    mbstring \
    zip \
    opcache

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

ARG PHP_ENV=development
COPY docker/php/php.${PHP_ENV}.ini /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g ${GROUP_ID} symfony && \
    adduser -D -u ${USER_ID} -G symfony symfony

WORKDIR /var/www

RUN chown -R symfony:symfony /var/www

USER symfony

EXPOSE 9000

CMD ["php-fpm"]
