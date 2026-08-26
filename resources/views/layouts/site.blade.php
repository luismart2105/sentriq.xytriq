<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#061a3a">
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="@yield('description', 'Soluciones de seguridad electrónica para hogares, negocios y empresas en Guadalajara y su Zona Metropolitana.')">

        <title>@yield('title', 'Sentriq') | Sentriq by Xytriq</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">

        <meta property="og:locale" content="es_MX">
        <meta property="og:type" content="website">
        <meta property="og:title" content="@yield('title', 'Sentriq')">
        <meta property="og:description" content="@yield('description', config('sentriq.brand.slogan'))">
        <meta property="og:image" content="{{ asset('assets/images/hero-home-security.png') }}">

        @php
            $structuredData = [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => config('sentriq.brand.full_name'),
                'description' => 'Soluciones de seguridad electrónica e instalaciones profesionales.',
                'areaServed' => config('sentriq.coverage'),
                'telephone' => config('sentriq.contact.whatsapp_display'),
                'email' => config('sentriq.contact.email'),
                'url' => url('/'),
                'sameAs' => [config('sentriq.contact.facebook')],
                'openingHours' => 'Mo-Sa 10:00-20:00',
            ];
        @endphp
        <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </head>
    <body>
        <a class="skip-link" href="#contenido">Saltar al contenido</a>

        <header class="site-header" data-header>
            <div class="container site-header__inner">
                <a class="site-brand" href="{{ route('home') }}" aria-label="Sentriq, ir al inicio">
                    <span class="site-brand__icon" aria-hidden="true">
                        <img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="" width="1280" height="1526">
                    </span>
                    <span class="site-brand__wordmark" aria-hidden="true">
                        <img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="" width="1280" height="1526">
                    </span>
                </a>

                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" data-nav-toggle>
                    <span></span><span></span><span></span>
                    <span class="sr-only">Abrir menú</span>
                </button>

                <nav id="main-navigation" class="main-nav" aria-label="Navegación principal" data-nav>
                    <a @class(['is-active' => request()->routeIs('home')]) href="{{ route('home') }}">Inicio</a>
                    <a @class(['is-active' => request()->routeIs('services.*')]) href="{{ route('services.index') }}">Servicios</a>
                    <a @class(['is-active' => request()->routeIs('about')]) href="{{ route('about') }}">Nosotros</a>
                    <a @class(['is-active' => request()->routeIs('warranty')]) href="{{ route('warranty') }}">Garantías</a>
                    <a @class(['is-active' => request()->routeIs('contact')]) href="{{ route('contact') }}">Contacto</a>
                    <a class="button button--small button--whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">
                        <x-icon name="whatsapp" />
                        Cotizar
                    </a>
                </nav>
            </div>
        </header>

        <main id="contenido">
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <a class="site-brand site-brand--footer" href="{{ route('home') }}" aria-label="Sentriq, ir al inicio">
                        <span class="site-brand__icon" aria-hidden="true">
                            <img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="" width="1280" height="1526">
                        </span>
                        <span class="site-brand__wordmark" aria-hidden="true">
                            <img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="" width="1280" height="1526">
                        </span>
                    </a>
                    <p>{{ config('sentriq.brand.slogan') }}</p>
                    <span class="illustrative-note">Las fotografías del sitio son ilustrativas.</span>
                </div>

                <div>
                    <h2>Explora</h2>
                    <ul>
                        <li><a href="{{ route('services.index') }}">Servicios</a></li>
                        <li><a href="{{ route('about') }}">Nosotros</a></li>
                        <li><a href="{{ route('warranty') }}">Garantías</a></li>
                        <li><a href="{{ route('contact') }}">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h2>Contacto</h2>
                    <ul>
                        <li><a href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">{{ config('sentriq.contact.whatsapp_display') }}</a></li>
                        <li><a href="mailto:{{ config('sentriq.contact.email') }}">{{ config('sentriq.contact.email') }}</a></li>
                        <li><a href="{{ config('sentriq.contact.facebook') }}" target="_blank" rel="noopener">Facebook</a></li>
                    </ul>
                </div>

                <div>
                    <h2>Atención</h2>
                    <p>{{ config('sentriq.contact.hours') }}</p>
                    <p>Guadalajara y Zona Metropolitana.</p>
                </div>
            </div>

            <div class="container footer-bottom">
                <p>&copy; {{ date('Y') }} Sentriq by Xytriq. Todos los derechos reservados.</p>
                <p>Seguridad electrónica instalada a conciencia.</p>
            </div>
        </footer>

        <a class="floating-whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener" aria-label="Contactar a Sentriq por WhatsApp">
            <x-icon name="whatsapp" />
        </a>

        <script src="{{ asset('assets/js/site.js') }}" defer></script>
    </body>
</html>
