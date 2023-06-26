FROM ghcr.io/jield-webdev/docker-repos/php8.2-cli:dev

LABEL maintainer="Johan van der Heide <johan.van.der.heide@itea4.org>"
LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/portal-backend/cli"

RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www