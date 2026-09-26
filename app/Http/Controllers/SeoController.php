<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $contents = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /opinar/',
            'Disallow: /presupuesto/',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($contents, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function sitemap(): Response
    {
        $urls = [
            route('home'),
            route('services.index'),
            ...array_map(
                fn (string $service): string => route('services.show', $service),
                array_keys(config('sentriq.services')),
            ),
            route('about'),
            route('warranty'),
            route('contact'),
            route('privacy'),
        ];

        return response()->view('sitemap', [
            'urls' => $urls,
        ], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
