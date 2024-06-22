FROM dunglas/frankenphp
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV SERVER_NAME=kertaskerja-atp.com
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN install-php-extensions pcntl zip
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /app
COPY . .
RUN composer install