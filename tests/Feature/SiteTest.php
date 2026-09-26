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
            '/aviso-de-privacidad',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_is_branded_and_indexable(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Protegemos tu espacio con soluciones hechas para ti.')
            ->assertSee('wa.me/523321231570', false)
            ->assertSee('index, follow', false)
            ->assertSee('<link rel="canonical" href="http://localhost">', false)
            ->assertDontSee('noindex, nofollow', false)
            ->assertDontSee('Deploy now');
    }

    public function test_robots_allows_public_pages_and_references_sitemap_without_long_term_cache(): void
    {
        $response = $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("User-agent: *\nAllow: /", false)
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: http://localhost/sitemap.xml', false);

        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=0', $response->headers->get('Cache-Control'));
    }

    public function test_sitemap_contains_every_indexable_public_page_without_long_term_cache(): void
    {
        $paths = [
            '/',
            '/servicios',
            ...array_map(
                fn (string $service): string => '/servicios/'.$service,
                array_keys(config('sentriq.services')),
            ),
            '/nosotros',
            '/garantias',
            '/contacto',
            '/aviso-de-privacidad',
        ];

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        foreach ($paths as $path) {
            $response->assertSee('<loc>http://localhost'.($path === '/' ? '' : $path).'</loc>', false);
        }

        $this->assertSame(count($paths), substr_count($response->getContent(), '<url>'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=0', $response->headers->get('Cache-Control'));
    }

    public function test_mail_autoconfig_uses_the_tls_mail_hostname(): void
    {
        $this->get('/.well-known/autoconfig/mail/config-v1.1.xml?emailaddress=support@sentriq.xytriq.com')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<hostname>mail.sentriq.xytriq.com</hostname>', false)
            ->assertSee('<port>993</port>', false)
            ->assertSee('<port>465</port>', false)
            ->assertSee('<username>%EMAILADDRESS%</username>', false);
    }

    public function test_mail_autodiscover_accepts_pox_requests_without_csrf(): void
    {
        $request = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/requestschema/2006">
    <Request><EMailAddress>support@sentriq.xytriq.com</EMailAddress></Request>
</Autodiscover>
XML;

        $this->call('POST', '/autodiscover/autodiscover.xml', content: $request, server: ['CONTENT_TYPE' => 'application/xml'])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<Server>mail.sentriq.xytriq.com</Server>', false)
            ->assertSee('<LoginName>support@sentriq.xytriq.com</LoginName>', false)
            ->assertSee('<Port>993</Port>', false)
            ->assertSee('<Port>465</Port>', false);
    }

    public function test_unknown_service_returns_not_found(): void
    {
        $this->get('/servicios/no-existe')->assertNotFound();
    }
}
