@extends('layouts.site')

@section('title', $service['name'])
@section('description', $service['summary'] . ' Servicio profesional en Guadalajara y Zona Metropolitana.')

@section('content')
    <section class="service-hero">
        <div class="container service-hero__grid">
            <div>
                <a class="back-link" href="{{ route('services.index') }}"><x-icon name="arrow" /> Todos los servicios</a>
                <span class="eyebrow">Soluciones Sentriq</span>
                <h1>{{ $service['name'] }}</h1>
                <p>{{ $service['intro'] }}</p>
                <a class="button button--whatsapp" href="https://wa.me/{{ config('sentriq.contact.whatsapp_number') }}?text={{ rawurlencode($service['whatsapp_message']) }}" target="_blank" rel="noopener">
                    <x-icon name="whatsapp" /> Solicitar asesoría
                </a>
            </div>
            <div class="service-hero__mark" aria-hidden="true">
                @php
                    $icons = [
                        'camaras-de-seguridad' => 'camera',
                        'automatizacion-de-portones' => 'gate',
                        'control-de-acceso' => 'access',
                        'alarmas' => 'alarm',
                        'cercas-electricas' => 'fence',
                        'acceso-vehicular' => 'vehicle',
                    ];
                @endphp
                <x-icon :name="$icons[$slug] ?? 'shield'" />
            </div>
        </div>
    </section>

    <section class="section section--light">
        <div class="container service-detail-grid">
            <div>
                <span class="eyebrow">Qué podemos integrar</span>
                <h2>Una solución completa y bien configurada</h2>
                <ul class="check-list">
                    @foreach ($service['features'] as $feature)
                        <li><x-icon name="check" /> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
            <aside class="consult-card">
                <x-icon name="shield" />
                <h3>La recomendación empieza por entender tu espacio</h3>
                <p>La tecnología correcta depende de accesos, distancias, hábitos y nivel de control requerido. Por eso valoramos cada proyecto antes de proponerte una solución.</p>
                <strong>Valoración y cotización sin costo para proyectos nuevos.</strong>
            </aside>
        </div>
    </section>

    @if ($kits->isNotEmpty())
        <section class="section" id="paquetes">
            <div class="container">
                <div class="section-heading section-heading--split">
                    <div>
                        <span class="eyebrow">Kits con instalación incluida</span>
                        <h2>Opciones listas para comenzar</h2>
                    </div>
                    <p>Elige la cobertura para tu casa o negocio. Revisa el equipo y el alcance de cada paquete; cualquier trabajo adicional se cotiza antes de instalar.</p>
                </div>

                <div class="kit-grid">
                    @foreach ($kits as $kit)
                        <article @class(['kit-card', 'kit-card--featured' => $kit->featured])>
                            @if ($kit->image_path || $kit->cabinet_image_path)
                                <div class="kit-card__media">
                                    @foreach (['image', 'cabinet_image'] as $imageField)
                                        @if ($kit->imageUrl($imageField.'_path'))
                                            <figure>
                                                <img src="{{ $kit->imageUrl($imageField.'_path') }}" alt="{{ $kit->getAttribute($imageField.'_caption') ?: $kit->name }}" width="400" height="300" loading="lazy" decoding="async">
                                                <figcaption>{{ $kit->getAttribute($imageField.'_caption') }}</figcaption>
                                            </figure>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if ($kit->featured)
                                <span class="kit-card__tag">Recomendado</span>
                            @endif
                            <h3>{{ $kit->name }}</h3>
                            @if ($kit->description)
                                <p>{{ $kit->description }}</p>
                            @endif
                            <strong class="kit-card__price">${{ number_format((float) $kit->price, 2) }} <small>MXN</small></strong>
                            @if ($kit->price_label)
                                <span class="kit-card__price-label">{{ $kit->price_label }}</span>
                            @endif
                            <ul class="check-list">
                                @foreach ($kit->features ?? [] as $feature)
                                    <li><x-icon name="check" /> {{ $feature }}</li>
                                @endforeach
                                @if ($kit->installation_included)
                                    <li><x-icon name="check" /> Instalación incluida</li>
                                @endif
                            </ul>
                            @if ($kit->conditions)
                                <details class="kit-card__conditions">
                                    <summary>Alcance y condiciones</summary>
                                    <p>{{ $kit->conditions }}</p>
                                </details>
                            @endif
                            <a class="button button--whatsapp" href="https://wa.me/{{ config('sentriq.contact.whatsapp_number') }}?text={{ rawurlencode('Hola Sentriq, me interesa el '.$kit->name.' con precio de $'.number_format((float) $kit->price, 2).' MXN.') }}" target="_blank" rel="noopener">
                                <x-icon name="whatsapp" /> Consultar este kit
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Beneficios</span>
                <h2>Más control, claridad y tranquilidad</h2>
            </div>
            <div class="benefit-grid">
                @foreach ($service['benefits'] as $benefit)
                    <article><span>0{{ $loop->iteration }}</span><p>{{ $benefit }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--light">
        <div class="container brands">
            <div>
                <span class="eyebrow">Equipos</span>
                <h2>Marcas con las que trabajamos</h2>
                <p>Seleccionamos el equipo de acuerdo con el alcance y presupuesto de cada proyecto.</p>
            </div>
            <ul>
                @foreach ($service['brands'] as $brand)
                    <li>{{ $brand }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="cta">
        <div class="container cta__inner">
            <div>
                <span class="eyebrow">Asesoría para tu proyecto</span>
                <h2>¿Necesitas {{ mb_strtolower($service['name']) }}?</h2>
                <p>Cuéntanos qué necesitas y coordinemos una valoración.</p>
            </div>
            <a class="button button--light" href="https://wa.me/{{ config('sentriq.contact.whatsapp_number') }}?text={{ rawurlencode($service['whatsapp_message']) }}" target="_blank" rel="noopener">
                <x-icon name="whatsapp" /> Hablar con Sentriq
            </a>
        </div>
    </section>
@endsection
