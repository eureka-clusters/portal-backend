FROM ghcr.io/jield-webdev/docker-repos/php8.2-fpm:dev

LABEL maintainer="Johan van der Heide <info@jield.nl>"
LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/backend/app"

RUN echo 'memory_limit = 4G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Copy composer.lock and composer.json
COPY composer.json* /var/www/

# Copy existing application directory contents
COPY .. /var/www

#Copy the global config files in
COPY ./.docker/app /var/www/config/autoload

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]