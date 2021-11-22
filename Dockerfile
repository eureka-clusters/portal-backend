FROM ghcr.io/jield-webdev/docker-repos/php80-prod:latest

MAINTAINER 'Johan van der Heide <info@jield.nl>'

LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/portal-backend"

# Copy composer.lock and composer.json
COPY composer.json* /var/www/

RUN echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Copy existing application directory contents
COPY ./ /var/www

#Copy the global config files in
COPY ./.docker/app /var/www/config/autoload

RUN composer install --no-dev --prefer-dist --no-interaction

EXPOSE 9000

CMD ["php-fpm"]
