FROM ghcr.io/jield-webdev/docker-repos/php80-prod:latest as php

MAINTAINER 'Johan van der Heide <info@jield.nl>'

LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/portal-backend"
LABEL org.opencontainers.image.description="Docker container holding the PHP backend code"

#set the workdir
WORKDIR /var/www

# Copy composer.lock and composer.json
COPY composer.json* /var/www/

RUN echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Copy existing application directory contents
COPY ./ /var/www

#Copy the global config files in
COPY ./.docker/app /var/www/config/autoload

RUN composer install --no-dev --prefer-dist --no-interaction

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

#Allow writing in the data folder
RUN chmod -R 777 data

# Change current user to www
USER www

EXPOSE 9000

CMD ["php-fpm"]


FROM nginx:latest as nginx

WORKDIR /var/www

#Copy the source code in the container (we don't need the full code)
COPY ./ /var/www

#create the necessary cache folders
RUN mkdir -p data
RUN chown -R 777 /data

#set some paths open
RUN chmod -R 777 data