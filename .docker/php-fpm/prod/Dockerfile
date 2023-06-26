FROM ghcr.io/jield-webdev/docker-repos/php8.2-fpm:latest

LABEL maintainer="Johan van der Heide <info@jield.nl>"
LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/backend/app"

RUN echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Copy existing application directory permissions
COPY . /var/www

#Copy the global config files in
COPY ./.docker/app /var/www/config/autoload

RUN composer install --no-dev --prefer-dist --no-interaction

#Make the data folder writeable
RUN chmod -R 777 data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]