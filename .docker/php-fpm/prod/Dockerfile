FROM ghcr.io/jield-webdev/docker-repos/php8.0-fpm:prod

LABEL maintainer="Johan van der Heide <info@jield.nl>"
LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/backend/app"

# Copy composer.lock and composer.json
COPY composer.json* /var/www/

# Copy existing application directory contents
COPY .. /var/www

#Copy the global config files in
COPY ./.docker/app /var/www/config/autoload

# Copy existing application directory permissions
COPY --chown=www:www .. /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]