FROM dunglas/frankenphp
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME=kertaskerja-atp.com
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN sed -i'' -e 's/^upload_max_filesize=.*/upload_max_filesize=20M/' "$PHP_INI_DIR/php.ini"
RUN sed -i'' -e 's/^post_max_size=.*/post_max_size=20M/' "$PHP_INI_DIR/php.ini"
RUN install-php-extensions pcntl zip pdo_mysql
WORKDIR /app
COPY . .
RUN composer install --no-dev