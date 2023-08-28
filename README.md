# portal-backend

The code of this portal is jointly developed by

Johan van der Heide <johan.van.der.heide@itea4.org>

## Documentation

All documentation can be found here: https://eureka-clusters.github.io/portal-backend/

```shell
docker exec -i pa-portal-backend-mysql mysql -u root -ppa-portal-root-password pa-portal < ecp_portal_backend.sql
```

## Generate proxies and other database manipulation

```shell
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:generate-proxies
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:validate-schema

docker compose run --rm cli php /var/www/composer.phar phpstan
docker compose run --rm cli php /var/www/composer.phar fix
docker compose run --rm cli php /var/www/composer.phar cs

docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:validate-schema
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:schema-tool:update --dump-sql

docker compose exec redis redis-cli
docker compose build
docker compose up -d


docker compose run --rm cli /var/www/bin/generate_templatemaps

./vendor/bin/openapi module -f json -o public/swagger/swagger.json
```