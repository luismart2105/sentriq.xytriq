<?php

namespace Tests\Feature;

use Tests\DatabaseTestCase;

class SiteTest extends DatabaseTestCase
{
    public function test_public_pages_are_available(): void
    {
        foreach ([
            '/',
            '/servicios',
            '/servicios/camaras-de-seguridad',
            '/servicios/automatizacion-de-portones',
            '/servicios/control-de-acceso',
            '/servicios/alarmas',
            '/servicios/cercas-electricas',
            '/servicios/acceso-vehicular',
            '/nosotros',
            '/garantias',
            '/contacto',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_is_branded_and_temporarily_not_indexable(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Protegemos tu espacio con soluciones hechas para ti.')
            ->assertSee('wa.me/523321231570', false)
            ->assertSee('noindex, nofollow', false)
            ->assertDontSee('Deploy now');
    }

    public function test_unknown_service_returns_not_found(): void
    {
        $this->get('/servicios/no-existe')->assertNotFound();
    }
}
