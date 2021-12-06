# portal-backend

The code of this portal is jointly developed by 

Johan van der Heide <johan.van.der.heide@itea4.org>
Benjamin Hoft <hoft@eurescom.eu>

```shell
docker exec -i pa-portal-backend-mysql mysql -u root -ppa-portal-root-password pa-portal < ecp_portal_backend.sql
```


### Generate proxies and other database manipulation

```shell
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:generate-proxies
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:validate-schema
docker compose run --rm cli /var/www/vendor/bin/doctrine-module orm:schema-tool:update --dump-sql
```