FROM dunglas/frankenphp
ENV SERVER_NAME=kertaskerja-atp.com
RUN install-php-extensions pcntl
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY . /app