# Catálogo de introducción Sentriq

Los paquetes se guardan en `kits`, no en las plantillas. Se administran en `/admin/kits`: nombre, precio, etiqueta de introducción, características, condiciones, fotografías, orden y publicación. La página pública es `/servicios/camaras-de-seguridad#paquetes`.

## Carga en cada entorno

```sh
php artisan migrate --force
php artisan storage:link
php artisan db:seed --class=IntroductionKitsSeeder --force
```

El importador crea únicamente los slugs que faltan. Ejecutarlo de nuevo no duplica ni sobrescribe las modificaciones del administrador. No usar `migrate:fresh` en una base con datos. El script actual de despliegue solo actualiza Git: estos comandos deben ejecutarse también en el servidor antes de servir las nuevas vistas. Resguardar primero la base de datos del entorno.

Las fotos iniciales están versionadas en `public/assets/images/kits`. Las nuevas cargas del administrador van a `storage/app/public/kits` y se sirven mediante `public/storage`. Respaldar esa carpeta además de la base. Al reemplazar o quitar una foto se conserva el archivo anterior para evitar pérdidas; ya no se referencia desde el catálogo.

## Oferta acordada

| Cámaras | Precio MXN | Cámara | Gabinete | Cable por cámara |
|---|---:|---|---|---|
| 4 | 6,500 | THC-B120-PS, con micrófono | PST-1929-14E + placa PST-1929-14EPL | 18 m |
| 6 | 8,000 | THC-B120-PC, sin micrófono | PST-2530-15A, placa incluida | 20 m |
| 8 | 9,500 | THC-B120-MC, sin micrófono | PST-2530-15A, placa incluida | 20 m |

Todos incluyen HDD de 1 TB agregado al kit base, grabación 1080p Lite, una caja LP-WBX-80 por cámara, alimentación, fijaciones, configuración y pruebas. El DVR queda fuera del gabinete. Instalación aparente; canalizaciones, obra civil y trabajos especiales se cotizan aparte. Traslado: hasta 100 km totales, no radio de 100 km. No se añadió una fecha de vencimiento ni se inventó un descuento sobre un precio anterior.

Revisar existencias, costos finales e impuestos de la cotización antes de vender. Los precios de introducción fueron fijados por el propietario; no constituyen una validación de margen. La capacidad de 1 TB no implica una cantidad fija de días de grabación.

## Fotografías reales y fuentes

Por indicación posterior del propietario se usan fotos de los modelos reales, sin redibujarlos mediante IA. Las fotografías no representan un trabajo de instalación realizado por Sentriq. La cantidad incluida se indica en el pie de foto; en el paquete de 6 se muestra una unidad del modelo de cámara, no un kit prearmado del proveedor.

- `hilook-thc-b120-ps-official.png`: [THC-B120-PS, fabricante HiLook](https://www.hilooksecurity.com/europe/products/hilook-turbo-hd-products/Turbo-HD-Camera/1080P/thc-b120-ps/).
- `hilook-thc-b120-pc.png`: [THC-B120-PC, Vigilantec](https://www.vigilantec.mx/products/hilook-by-hikvision-thc-b120-pc).
- `hilook-hl1080p8b.png`: [HL1080P8B, Vigilantec](https://www.vigilantec.mx/products/kit-turbohd-dvr-8-canales-1080p-lite-8-camaras-bala-1080p-metalicas-8-cables-de-20-metros-2-fuentes-de-4-canales).
- `precision-pst-1929-14e.png`: [gabinete ABS, Vigilantec](https://www.vigilantec.mx/products/precisionpst-1929-14e).
- `precision-pst-2530-15a.png`: [gabinete de acero, Vigilantec](https://www.vigilantec.mx/products/precision-pst-2530-15a).
- `hilook-hl-1080-ps4.png`: [empaque del kit de 4, Vigilantec](https://www.vigilantec.mx/products/hilook-by-hikvision-hl-1080-ps4); referencia auxiliar, no usada como foto principal.

Fuentes consultadas el 2 de septiembre de 2026. No se afirma una licencia de redistribución de las fotografías; confirmar con los proveedores su autorización comercial antes de una campaña externa.

## Borradores generados, descartados

Se utilizó la habilidad imagegen con la herramienta integrada, antes de la indicación de usar fotos reales. Esos borradores no se publican ni se referencian en el proyecto. El prompt base fue:

> Use case: product-mockup. Asset type: landscape 3:2 website CCTV package catalog illustration. Clean photorealistic studio product arrangement, white and pale gray backdrop, soft shadows, realistic compact white bullet security cameras with black circular lens faces, a single small black DVR, one silver internal 3.5 inch hard drive, neatly coiled black siamese cable, white square junction boxes and a modest closed light gray wall electrical cabinet. Generous margins, equipment fully visible, tasteful catalog composition. No text, no numbers, no logos, no watermark, no monitor, no phone, no dome or PTZ cameras, no antennas, no luxury equipment. This is a generic illustrative render, not a branded product photograph.

Se añadió para cada variante: `EXACTLY N bullet cameras, arranged in two clearly separated rows of N/2, and N small square junction boxes. One DVR, one HDD and one cabinet only.`, con N = 4, 6 y 8.
