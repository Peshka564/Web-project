# Web Project

## Extract default config files

> **_NOTE:_** For first-time setup, comment out the configuration volume mounts in compose.yml (php.ini and apache.conf). Start the container once, copy the default config files using the commands below, then uncomment the mounts.

```bash
docker cp php_web:/usr/local/etc/php/php.ini-development ./config/php.ini
```

```bash
docker cp php_web:/etc/apache2/sites-available/000-default.conf ./config/apache.conf
```

## Run Unit tests locally

```bash
php test/index.php
```

## DB setup
1. Start container - `docker compose up`
2. Go into the MariaDB container - `docker exec -it mariadb_container mariadb -u admin -padmin demo`
3. Create the tables - `SOURCE /schema/schema.sql`