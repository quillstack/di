FROM php:cli
RUN apt update -y && apt upgrade -y && apt-get install -y libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-configure zip
CMD curl https://getcomposer.org/composer-stable.phar --output /usr/local/bin/composer && \
chmod a+x /usr/local/bin/composer && \
tail -f /dev/null
