FROM php:8.5-fpm-alpine

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
    zip

# Install Redis and APCu via PECL
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis apcu \
    && docker-php-ext-enable redis apcu \
    && apk del $PHPIZE_DEPS

ARG PHP_ENV=development

# Install Xdebug only in development
RUN if [ "$PHP_ENV" = "development" ]; then \
    apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS linux-headers; \
    fi

COPY docker/php/php.${PHP_ENV}.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug-custom.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g ${GROUP_ID} symfony && \
    adduser -D -u ${USER_ID} -G symfony symfony

WORKDIR /var/www

RUN chown -R symfony:symfony /var/www

USER symfony

EXPOSE 9000

CMD ["php-fpm"]
