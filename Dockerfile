FROM dunglas/frankenphp
ENV SERVER_NAME=kertaskerja-atp.com
RUN install-php-extensions pcntl
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
WORKDIR /app
COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install