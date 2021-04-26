FROM php:fpm-alpine

# Sets default development PHP configuration
RUN mv ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

# Sets PHP configuration
COPY ./docker/prod/php/config/php.conf ${PHP_INI_DIR}/conf.d/php.ini

# Copies the service directory
COPY . /service
