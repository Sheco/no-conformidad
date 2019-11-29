#  Introducción

Este software soporta ser ejecutado bajo docker.

Las instrucciones basicas para arrancar el entorno es:

```
docker-compose up -d
docker cp storage noconformidad_app_1 app:/app/
docker-compose exec app touch /app/storage/database.sqlite
docker-compose exec app php /app/artisan migrate
docker-compose exec app php /app/artisan db:seed
```

En el archivo public/.htaccess, agregar lo siguiente:

```
RewriteRule ^([^\.]+\.php)(/.*)?$ fcgi://localhost:9000/app/public/$1 [L,P]
```

