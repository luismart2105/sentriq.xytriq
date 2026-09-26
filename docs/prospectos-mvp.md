# MVP de prospectos

## Decisiones técnicas

- El CRM usa la autenticación `auth` existente y no expone endpoints de lectura públicos.
- `prospects` conserva la ficha actual; `prospect_activities` conserva notas, contactos y transiciones de etapa con fecha y usuario.
- `quotes.prospect_id` es nullable y usa `nullOnDelete`, por lo que los presupuestos históricos continúan funcionando sin relación.
- Los clics a WhatsApp se guardan como eventos anónimos en `whatsapp_clicks`. No crean prospectos ni se presentan como conversaciones.
- Las UTM se conservan durante la sesión. La fuente del formulario se fija en servidor como `web`; los campos ocultos no deciden la fuente.
- El formulario está apagado por defecto hasta configurar y probar el correo. El aviso fue aprobado el 25 de septiembre de 2026; el CRM administrativo y la medición de clics no dependen de esta bandera.
- La notificación implementa tres intentos si se configura una cola asíncrona. Con la infraestructura actual (`QUEUE_CONNECTION=sync`) se intenta después de guardar y cualquier falla queda en el log sin revertir el prospecto.
- Los formularios aceptan proyectos nuevos y solicitudes de soporte o reparación, se asignan a la cuenta `support@sentriq.xytriq.com` y comunican un plazo de primera respuesta de 24 horas hábiles.
- Los prospectos no convertidos se eliminan tres meses después del último contacto. La tarea `prospects:prune` se programa diariamente; los prospectos ganados y aquellos con un presupuesto aceptado o firmado quedan excluidos.

## Configuración pendiente antes de habilitar el formulario

```dotenv
SENTRIQ_LEAD_FORM_ENABLED=false
SENTRIQ_LEAD_NOTIFICATION_EMAIL=support@sentriq.xytriq.com
SENTRIQ_LEAD_ASSIGNEE_EMAIL=support@sentriq.xytriq.com
SENTRIQ_LEAD_RETENTION_MONTHS=3
```

El aviso de privacidad, el correo `support@sentriq.xytriq.com`, la atención de proyectos y soporte, el responsable y el plazo de respuesta fueron aprobados por Luisangel. La configuración observada en producción usa `MAIL_MAILER=log`, por lo que debe configurarse un transporte de correo real antes de esperar avisos por email. Después de cambiar variables, ejecutar `php artisan config:clear`.

## Despliegue

El cron actual solamente hace un avance rápido de `origin/main`; no ejecuta migraciones. Coordinar el merge y aplicar estos pasos en la copia de producción antes de verificar el panel:

```bash
cd /home/zauryx/web/sentriq.xytriq.com/public_html
php artisan migrate --force
php artisan optimize:clear
php artisan migrate:status
php artisan prospects:prune --dry-run
```

No activar todavía `SENTRIQ_LEAD_FORM_ENABLED`. Primero comprobar:

1. `/admin/prospectos` y `/admin/prospectos/hoy` requieren sesión y cargan sin errores.
2. Crear manualmente un prospecto de prueba, cambiar su etapa, agregar una actividad y programar seguimiento.
3. Crear un presupuesto relacionado y confirmar que la ficha pasa a Cotización.
4. Abrir un enlace de WhatsApp y confirmar un registro en `whatsapp_clicks`, separado de `prospects`.
5. Configurar y probar el correo. Después cambiar `SENTRIQ_LEAD_FORM_ENABLED=true`, limpiar configuración y enviar un formulario de proyecto y otro de soporte.
6. Confirmar que portada, servicios, `robots.txt`, `sitemap.xml`, firma de presupuestos y reseñas siguen disponibles.

Para que la retención se aplique automáticamente, verificar que el programador de Laravel se ejecute cada minuto en el servidor:

```cron
* * * * * cd /home/zauryx/web/sentriq.xytriq.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

## Reversión

La reversión de código debe preceder cualquier rollback de base. `php artisan migrate:rollback --step=3` elimina las tablas del CRM y la relación; hacerlo borraría los prospectos capturados, por lo que requiere respaldo y una decisión explícita.
