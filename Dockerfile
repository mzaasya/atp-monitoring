FROM dunglas/frankenphp
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME=kertaskerja-atp.com
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN sed -i 's/upload_max_filesize = 20M/upload_max_filesize = 128M/g' /usr/local/etc/php/php.ini
RUN install-php-extensions pcntl zip pdo_mysql
WORKDIR /app
COPY . .
RUN composer install --no-dev