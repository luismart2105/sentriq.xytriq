@extends('layouts.site')

@section('title', 'Seguridad electrónica en Guadalajara')
@section('description', 'Cámaras de seguridad, automatización de portones, controles de acceso, alarmas y más para hogares en Guadalajara y su Zona Metropolitana.')

@section('content')
    <section class="hero">
        <div class="container hero__grid">
            <div class="hero__content">
                <span class="eyebrow">Seguridad electrónica en Guadalajara</span>
                <h1>Protegemos tu espacio con soluciones hechas para ti.</h1>
                <p>No se trata solo de instalar equipos. Evaluamos tu espacio, entendemos lo que necesitas y te ayudamos a elegir una solución funcional, confiable y bien instalada.</p>

                <div class="hero__actions">
                    <a class="button button--whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">
                        <x-icon name="whatsapp" />
                        Cotizar por WhatsApp
                    </a>
                    <a class="button button--ghost" href="{{ route('services.index') }}">Ver servicios</a>
                </div>

                <ul class="hero__trust" aria-label="Ventajas de Sentriq">
                    <li><x-icon name="check" /> Valoración y cotización gratuitas</li>
                    <li><x-icon name="check" /> 2 años de garantía en instalación</li>
                </ul>
            </div>

            <div class="hero__visual">
                <img src="{{ asset('assets/images/hero-home-security.png') }}" alt="Instalación ilustrativa de una cámara de seguridad en una residencia" width="1717" height="916">
                <div class="hero__badge">
                    <x-icon name="shield" />
                    <span><strong>Instalación a conciencia</strong>Soluciones pensadas para durar</span>
                </div>
            </div>
        </div>
    </section>

    <section class="trust-bar" aria-label="Información principal">
        <div class="container trust-bar__grid">
            <div><strong>2 años</strong><span>de garantía en instalación</span></div>
            <div><strong>Sin costo</strong><span>valoración para proyectos nuevos</span></div>
            <div><strong>Atención local</strong><span>Guadalajara y Zona Metropolitana</span></div>
            <div><strong>Asesoría real</strong><span>equipo adecuado para cada espacio</span></div>
        </div>
    </section>

    <section class="section section--light">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">Soluciones para proteger tu espacio</span>
                    <h2>Seguridad que se adapta a tu hogar</h2>
                </div>
                <p>Desde videovigilancia hasta el control de tus accesos, diseñamos cada proyecto alrededor de tus necesidades.</p>
            </div>

            <div class="service-grid">
                @foreach (config('sentriq.services') as $slug => $service)
                    <x-service-card :slug="$slug" :service="$service" />
                @endforeach
            </div>

            <div class="section-action">
                <a class="text-link" href="{{ route('services.index') }}">Explorar todos los servicios <x-icon name="arrow" /></a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container feature-split">
            <div class="feature-panel">
                <span class="eyebrow">Nuestro compromiso</span>
                <h2>Una instalación bien hecha también es parte de tu seguridad</h2>
                <p>Trabajamos con cuidado en cada conexión, configuración y detalle de instalación. Por eso respaldamos nuestro trabajo durante más tiempo de lo habitual.</p>
                <a class="button button--outline" href="{{ route('warranty') }}">Conocer nuestras garantías</a>
            </div>

            <div class="warranty-grid">
                <article>
                    <span class="big-number">2</span>
                    <h3>Años en instalación</h3>
                    <p>Cobertura contra defectos derivados del trabajo realizado por Sentriq.</p>
                </article>
                <article>
                    <span class="big-number">1</span>
                    <h3>Año en equipos</h3>
                    <p>Garantía contra defectos de fábrica conforme a las condiciones del fabricante.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--light">
        <div class="container">
            <div class="section-heading section-heading--center">
                <span class="eyebrow">Así trabajamos</span>
                <h2>Un proceso claro, de principio a fin</h2>
                <p>Te acompañamos desde la primera conversación hasta el soporte posterior a la instalación.</p>
            </div>

            <ol class="process">
                <li><span>1</span><h3>Cuéntanos tu idea</h3><p>Escríbenos por WhatsApp y dinos qué quieres proteger o automatizar.</p></li>
                <li><span>2</span><h3>Valoración gratuita</h3><p>Revisamos el espacio y entendemos las necesidades del proyecto.</p></li>
                <li><span>3</span><h3>Propuesta adecuada</h3><p>Recibes opciones claras y una cotización acorde con el alcance.</p></li>
                <li><span>4</span><h3>Instalación y soporte</h3><p>Instalamos, configuramos, explicamos el uso y respaldamos el trabajo.</p></li>
            </ol>
        </div>
    </section>

    <section class="section">
        <div class="container coverage-card">
            <div>
                <span class="eyebrow">Estamos cerca de ti</span>
                <h2>Servicio en Guadalajara y alrededores</h2>
                <p>Atendemos proyectos en Guadalajara, Zapopan, Tonalá, Tlaquepaque, Tlajomulco y el resto de la Zona Metropolitana.</p>
            </div>
            <div class="coverage-card__action">
                <p>¿No sabes si llegamos a tu zona?</p>
                <a class="button button--whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">
                    <x-icon name="whatsapp" /> Consultar cobertura
                </a>
            </div>
        </div>
    </section>

    @if ($reviews->isNotEmpty())
        <section class="section section--light">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow">Experiencias reales</span>
                    <h2>Lo que dicen nuestros clientes</h2>
                    <p>Reseñas enviadas mediante invitación privada y aprobadas antes de publicarse.</p>
                </div>

                <div class="review-grid">
                    @foreach ($reviews as $review)
                        <article class="review-card">
                            @if ($review->photo_path)
                                <img class="review-card__photo" src="{{ asset('storage/'.$review->photo_path) }}" alt="Fotografía compartida por {{ $review->publicName() }}" loading="lazy">
                            @endif
                            <div class="review-card__stars" aria-label="{{ $review->rating }} de 5 estrellas">
                                {{ str_repeat('★', $review->rating) }}
                            </div>
                            <x-icon name="quote" />
                            <blockquote>{{ $review->comment }}</blockquote>
                            <footer>
                                <strong>{{ $review->publicName() }}</strong>
                                <span>{{ $review->municipality }} · {{ $review->service }}</span>
                            </footer>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="cta">
        <div class="container cta__inner">
            <div>
                <span class="eyebrow">Empieza con una valoración sin costo</span>
                <h2>Hablemos de cómo proteger tu espacio</h2>
                <p>Explícanos qué necesitas y te orientaremos para elegir una solución adecuada.</p>
            </div>
            <a class="button button--light" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">
                <x-icon name="whatsapp" /> Escribir por WhatsApp
            </a>
        </div>
    </section>
@endsection
