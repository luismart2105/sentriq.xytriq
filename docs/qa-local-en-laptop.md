# Ejecutar Sentriq localmente con la base QA del VPS

Este flujo ejecuta el código de Sentriq en tu laptop con `php artisan serve`; no crea otro sitio público. La aplicación local se conecta a la base QA de MariaDB que vive en el VPS:

- Host: `104.254.246.40`
- Puerto: `3306`
- Base: `zauryx_sentriq_qa`
- Usuario: `zauryx_sentriq_qa`

## Requisitos

- PHP 8.3 o posterior, Composer 2 y las extensiones PHP `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`, `fileinfo` y `dom`.
- Node.js y npm para compilar los recursos del sitio.
- Git y conectividad de salida desde la laptop al puerto TCP `3306` del VPS.

## Preparar el proyecto

Clona el repositorio y usa la rama que quieras probar. Desde la carpeta del proyecto ejecuta:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
```

En Windows PowerShell, usa `Copy-Item .env.example .env` en lugar de `cp`.

Edita `.env` y configura estos valores. Sustituye `PEGA_AQUI_LA_CLAVE_DE_QA` por la contraseña QA obtenida de forma segura del administrador del VPS; no la guardes en Git ni la compartas en tickets:

```dotenv
APP_NAME=Sentriq
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=104.254.246.40
DB_PORT=3306
DB_DATABASE=zauryx_sentriq_qa
DB_USERNAME=zauryx_sentriq_qa
DB_PASSWORD=PEGA_AQUI_LA_CLAVE_DE_QA

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
SENTRIQ_LEAD_FORM_ENABLED=false
```

Si editas `.env` después de haber iniciado Laravel, limpia su configuración cacheada:

```bash
php artisan config:clear
```

No ejecutes `php artisan migrate`, `migrate:fresh`, `db:seed` ni `composer setup` contra QA como parte del inicio local. El esquema de QA se refresca desde producción; los cambios de esquema deben prepararse como migraciones y coordinarse antes de aplicarlos.

## Comprobar la conexión y arrancar

Primero, desde la laptop confirma que el puerto TCP es accesible:

```bash
nc -vz 104.254.246.40 3306
```

En Windows PowerShell:

```powershell
Test-NetConnection 104.254.246.40 -Port 3306
```

Después confirma que Laravel puede autenticarse y consultar la base:

```bash
php artisan tinker --execute="DB::connection()->select('SELECT 1')"
```

Finalmente, inicia el servidor de desarrollo:

```bash
php artisan serve
```

Abre `http://127.0.0.1:8000` en la laptop. Para levantar Vite con recarga de cambios del frontend, usa `npm run dev` en otra terminal.

## Datos y seguridad

La base QA se reemplaza diariamente con una copia de producción. Los datos creados o editados en QA pueden desaparecer en la siguiente sincronización. El volcado de la base no copia archivos de `storage/`, así que algunas imágenes u otros adjuntos pueden no estar disponibles localmente.

El puerto MySQL del VPS acepta conexiones remotas para permitir IPs dinámicas. Usa únicamente el usuario QA, cuya autorización se limita a `zauryx_sentriq_qa`; nunca configures el usuario o la base de producción en la laptop. Mantén `.env` fuera del repositorio y protege la contraseña QA.
