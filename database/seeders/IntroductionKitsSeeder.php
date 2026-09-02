<?php

namespace Database\Seeders;

use App\Models\Kit;
use Illuminate\Database\Seeder;

class IntroductionKitsSeeder extends Seeder
{
    public function run(bool $refreshDescriptions = false): void
    {
        $conditions = "Antes de instalar, revisamos tu espacio y confirmamos qué incluye el trabajo. Los precios de introducción están sujetos a disponibilidad.\nIncluye la colocación de las cámaras y el cable visible, sujeto a la pared. Si necesitas ocultar el cable, agregar más metros o realizar trabajos especiales, te cotizamos la diferencia antes de comenzar.\nNecesitas una toma de corriente cerca del grabador e internet para ver las cámaras desde tu celular. No incluye pantalla ni batería de respaldo.\nEl gabinete protege las conexiones y la alimentación de las cámaras. El grabador se coloca por separado.\nIncluye disco duro de 1 TB. El tiempo de grabación disponible depende del uso y la configuración.\nConsulta nuestra sección de Garantías para conocer el respaldo de tu instalación y tus equipos.";

        foreach ([
            [4, 6500, 'Hogar', 'Para cubrir accesos y puntos clave de tu casa o local pequeño, con audio integrado.', 'THC-B120-PS', 18, 'ABS PST-1929-14E de 190 × 290 × 140 mm con placa interna', 'precision-pst-1929-14e.png', 'hilook-thc-b120-ps-official.png'],
            [6, 8000, 'Negocio', 'Para distribuir la vigilancia entre entradas, caja, pasillos y áreas de trabajo.', 'THC-B120-PC', 20, 'acero PST-2530-15A de 250 × 300 × 150 mm con placa interna', 'precision-pst-2530-15a.png', 'hilook-thc-b120-pc.png'],
            [8, 9500, 'Cobertura', 'Para casas amplias y pequeños negocios que necesitan vigilar más zonas.', 'THC-B120-MC', 20, 'acero PST-2530-15A de 250 × 300 × 150 mm con placa interna', 'precision-pst-2530-15a.png', 'hilook-hl1080p8b.png'],
        ] as [$count, $price, $name, $description, $camera, $meters, $cabinet, $cabinetPhoto, $photo]) {
            // Import once: rerunning must never overwrite edits made in the admin.
            $data = [
                'service_slug' => 'camaras-de-seguridad',
                'name' => 'Sentriq '.$name.' · '.$count.' cámaras',
                'camera_count' => $count,
                'price' => $price,
                'price_label' => 'Precio de introducción',
                'description' => $description,
                'features' => [
                    $count.' cámaras HiLook con visión nocturna',
                    $count === 4 ? 'Con micrófono para escuchar lo que sucede' : 'Vigilancia de video, sin grabación de audio',
                    'Grabador para guardar y consultar tus videos',
                    'Disco duro de 1 TB incluido e instalado',
                    'Hasta '.$meters.' metros de cable por cámara',
                    'Una caja protectora de conexiones por cámara',
                    'Gabinete para mantener las conexiones ordenadas',
                    'Alimentación eléctrica para todas las cámaras',
                    'Grapas, tornillos y taquetes incluidos',
                    'Configuración en tu celular y explicación de uso',
                    'Entrega con pruebas de funcionamiento',
                ],
                'conditions' => $conditions,
                'image_path' => 'assets/images/kits/'.$photo,
                'image_caption' => 'Incluye '.$count.' cámaras HiLook',
                'cabinet_image_path' => 'assets/images/kits/'.$cabinetPhoto,
                'cabinet_image_caption' => 'Gabinete incluido',
                'installation_included' => true,
                'active' => true,
                'featured' => $count === 4,
                'sort_order' => $count,
            ];
            $kit = Kit::firstOrCreate(['slug' => 'sentriq-'.$count.'-camaras'], $data);

            if ($refreshDescriptions) {
                $kit->update(array_intersect_key($data, array_flip([
                    'description', 'features', 'conditions', 'image_caption', 'cabinet_image_caption',
                ])));
            }
        }
    }
}
