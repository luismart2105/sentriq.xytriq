# MVP de prospectos

## Decisiones técnicas

- El CRM usa la autenticación `auth` existente y no expone endpoints de lectura públicos.
- `prospects` conserva la ficha actual; `prospect_activities` conserva notas, contactos y transiciones de etapa con fecha y usuario.
- `quotes.prospect_id` es nullable y usa `nullOnDelete`, por lo que los presupuestos históricos continúan funcionando sin relación.
- Los clics a WhatsApp se guardan como eventos anónimos en `whatsapp_clicks`. No crean prospectos ni se presentan como conversaciones.
- Las UTM se conservan durante la sesión. La fuente del formulario se fija en servidor como `web`; los campos ocultos no deciden la fuente.
- El formulario está apagado por defecto hasta configurar y probar el correo. El aviso fue aprobado el 25 de septiembre de 2026; el CRM administrativo y la medición de clics no dependen de esta bandera.
- Las notificaciones al administrador y al cliente implementan tres intentos si se configura una cola asíncrona. Con la infraestructura actual (`QUEUE_CONNECTION=sync`) se intentan después de guardar y cualquier falla queda en el log sin revertir el prospecto.
- Los formularios aceptan proyectos nuevos y solicitudes de soporte o reparación, se asignan a la cuenta `support@sentriq.xytriq.com` y comunican un plazo de primera respuesta de 24 horas hábiles.
- Después del envío, el visitante llega a `/contacto/gracias`. Si proporcionó correo, recibe además una confirmación con el servicio, la zona y el mismo plazo de respuesta; no se incluyen notas internas.
- Los prospectos no convertidos se eliminan tres meses después del último contacto. La tarea `prospects:prune` se programa diariamente; los prospectos ganados y aquellos con un presupuesto aceptado o firmado quedan excluidos.

## Configuración del formulario y estado en producción

```dotenv
SENTRIQ_LEAD_FORM_ENABLED=false
SENTRIQ_LEAD_NOTIFICATION_EMAIL=support@sentriq.xytriq.com
SENTRIQ_LEAD_ASSIGNEE_EMAIL=support@sentriq.xytriq.com
SENTRIQ_LEAD_RETENTION_MONTHS=3
```

`SENTRIQ_LEAD_FORM_ENABLED=false` es el valor seguro por defecto para entornos nuevos; no describe el estado de producción. El propietario confirmó un envío exitoso desde el formulario de producción el 29 de octubre de 2026. El servidor aplica actualizaciones de `main` automáticamente cada dos minutos.

El aviso de privacidad, el correo `support@sentriq.xytriq.com`, la atención de proyectos y soporte, el responsable y el plazo de respuesta fueron aprobados por Luisangel. En producción, `support` es un buzón propio y Laravel usa el SMTP local mediante `smtp://127.0.0.1:25?auto_tls=false`; esto evita depender de `proc_open`, deshabilitado en PHP-FPM. El envío exitoso confirma que el flujo web responde, pero no confirma por sí solo la entrega de los correos. Después de cambiar variables de correo, ejecutar `php artisan config:clear` y comprobar la aceptación del mensaje en el registro de Exim.

## Despliegue

El despliegue automático hace un avance rápido de `origin/main`; no ejecuta migraciones ni seeders. El propietario confirmó que producción recoge los cambios de `main` cada dos minutos. La página pública de cámaras muestra los tres paquetes del catálogo. Ejecutar los siguientes pasos al introducir cambios de esquema o preparar un entorno nuevo; no usar `migrate:fresh` en una base con datos:

```bash
cd /home/zauryx/web/sentriq.xytriq.com/public_html
php artisan migrate --force
php artisan optimize:clear
php artisan migrate:status
php artisan prospects:prune --dry-run
```

## Verificación operativa pendiente

El envío público del formulario fue confirmado por el propietario en producción el 29 de octubre de 2026. La página de cámaras y los paquetes de 4, 6 y 8 cámaras también se comprobaron públicamente. Aún falta comprobar:

1. Confirmar en una sesión administrativa que el envío reciente aparece en `/admin/prospectos` y que `/admin/prospectos/hoy` carga sin errores.
2. Crear manualmente un prospecto, cambiar su etapa, agregar una actividad y programar seguimiento.
3. Crear un presupuesto relacionado y confirmar que la ficha pasa a Cotización.
4. Abrir un enlace de WhatsApp y confirmar un registro en `whatsapp_clicks`, separado de `prospects`. Las conversaciones reales de WhatsApp deben registrarse manualmente como prospectos.
5. Confirmar la entrega del aviso a `support@sentriq.xytriq.com` y del correo de confirmación al cliente. La activación del formulario ya no está pendiente.
6. Confirmar la instalación del cron del servidor para `schedule:run` y que la retención de prospectos funciona.
7. Confirmar que portada, servicios, `robots.txt`, `sitemap.xml`, firma de presupuestos y reseñas siguen disponibles.

Para que la retención se aplique automáticamente, verificar que el programador de Laravel se ejecute cada minuto en el servidor:

```cron
* * * * * cd /home/zauryx/web/sentriq.xytriq.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

## Reversión

La reversión de código debe preceder cualquier rollback de base. `php artisan migrate:rollback --step=3` elimina las tablas del CRM y la relación; hacerlo borraría los prospectos capturados, por lo que requiere respaldo y una decisión explícita.
