# Bases de datos de Sentriq en Hestia

La aplicación de producción se conecta por Laravel a MySQL en `127.0.0.1`. En Hestia, los nombres de las bases y de sus usuarios son:

- Producción: `zauryx_sentriq_prod`
- QA: `zauryx_sentriq_qa`

Las contraseñas no se guardan en Git. La aplicación productiva obtiene sus valores de `.env`. Las credenciales para conectar una futura aplicación QA están en `/root/.config/sentriq/qa-db.env` con permisos restringidos.

## Sincronización diaria de QA

La tarea nocturna reemplaza todas las tablas y vistas de QA con un volcado consistente de producción. No copia los archivos de `storage/`; los datos de la base pueden referirse a archivos que QA aún no tiene.

El script fuente está en `scripts/sync-qa-database.sh`. En el VPS se instala como `/usr/local/sbin/sentriq-qa-db-sync`; la regla en `deploy/sentriq-qa-db-sync.cron` lo ejecuta diariamente a las 03:15, hora local del servidor. El script usa la configuración local de MariaDB en `/root/.my.cnf`, escribe el resultado en `/var/log/sentriq-qa-db-sync.log` y elimina el volcado temporal al terminar.

Para instalar o actualizar esos dos archivos en el VPS:

```bash
install -o root -g root -m 700 scripts/sync-qa-database.sh /usr/local/sbin/sentriq-qa-db-sync
install -o root -g root -m 644 deploy/sentriq-qa-db-sync.cron /etc/cron.d/sentriq-qa-db-sync
```

La programación solamente sincroniza la base; no despliega ni crea un sitio QA. La conexión de producción permanece en `.env` y no se modifica por esta tarea.
