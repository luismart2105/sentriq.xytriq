# Autoconfiguración de correo en HestiaCP

## Servicios publicados

El dominio de correo `sentriq.xytriq.com` anuncia estos servidores:

- IMAP: `mail.sentriq.xytriq.com:993`, SSL/TLS.
- SMTP: `mail.sentriq.xytriq.com:465`, SSL/TLS.
- Submission alternativo: `mail.sentriq.xytriq.com:587`, STARTTLS.
- Usuario: dirección de correo completa.

Laravel publica las respuestas de descubrimiento en:

- `https://autoconfig.sentriq.xytriq.com/mail/config-v1.1.xml`
- `https://sentriq.xytriq.com/.well-known/autoconfig/mail/config-v1.1.xml`
- `https://autodiscover.sentriq.xytriq.com/autodiscover/autodiscover.xml`

La respuesta de Autodiscover acepta solicitudes POX por `POST` y no comprueba si existe el buzón, para no permitir enumeración de cuentas.

## Configuración de HestiaCP

`autoconfig.sentriq.xytriq.com` y `autodiscover.sentriq.xytriq.com` son alias web de `sentriq.xytriq.com`. El certificado web de Let's Encrypt incluye:

- `sentriq.xytriq.com`
- `www.sentriq.xytriq.com`
- `autoconfig.sentriq.xytriq.com`
- `autodiscover.sentriq.xytriq.com`

La redirección de dominio incorporada de Hestia está desactivada porque redirigiría también los hosts de descubrimiento. Los archivos incluidos `nginx.conf_www-redirect` y `nginx.ssl.conf_www-redirect` redirigen solamente `www` al dominio canónico.

La plantilla Nginx `laravel.tpl`/`laravel.stpl` no debe declarar un bloque `location ^~ /.well-known/acme-challenge/`. Ese bloque impediría que el bloque temporal de Hestia respondiera al desafío HTTP-01 de Let's Encrypt. La inclusión `nginx.conf_*` de Hestia debe manejar el desafío.

## DNS

Además de los SRV generados originalmente por Hestia, existen:

```text
autoconfig             A     104.254.246.40
autodiscover           A     104.254.246.40
_autodiscover._tcp     SRV   1 0 443 autodiscover.sentriq.xytriq.com.
_submissions._tcp      SRV   1 0 465 mail.sentriq.xytriq.com.
```

## Verificación después de una reconstrucción

1. Confirmar que ambos hosts resuelven en los DNS autoritativos primario y secundario.
2. Confirmar que las dos rutas HTTPS responden `200` con `Content-Type: application/xml`.
3. Validar que el XML anuncie exclusivamente `mail.sentriq.xytriq.com`.
4. Verificar el certificado por nombre para `autoconfig` y `autodiscover`.
5. Confirmar que `https://www.sentriq.xytriq.com/` continúa redirigiendo a `https://sentriq.xytriq.com/`.

Los clientes no implementan todos los mecanismos de descubrimiento de la misma manera. Estos endpoints complementan los registros SRV; no obligan a un cliente que ignore ambos estándares.
