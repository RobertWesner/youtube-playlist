FROM php:8.4-fpm

RUN echo 'memory_limit = 2048M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

RUN apt update && apt -y install git zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

COPY . /var/www/html

COPY ./docker/php-fpm/run.sh /run.sh
RUN chmod +x /run.sh

WORKDIR /var/www/html

RUN git config --global --add safe.directory '*'
RUN composer update

CMD ["/run.sh"]
