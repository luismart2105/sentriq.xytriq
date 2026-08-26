@extends('layouts.site')

@section('title', 'Contacto')
@section('description', 'Solicita una valoración gratuita para tu proyecto de seguridad electrónica en Guadalajara y Zona Metropolitana.')

@section('content')
    <section class="page-hero">
        <div class="container page-hero__inner">
            <span class="eyebrow">Hablemos de tu proyecto</span>
            <h1>Cuéntanos qué necesitas proteger o automatizar</h1>
            <p>La valoración y cotización para proyectos nuevos son gratuitas dentro de nuestra zona de atención.</p>
        </div>
    </section>

    <section class="section section--light">
        <div class="container contact-grid">
            <div class="contact-primary">
                <span class="eyebrow">Atención directa</span>
                <h2>La forma más rápida es WhatsApp</h2>
                <p>Envíanos una breve descripción del proyecto, ubicación aproximada y, si puedes, fotografías del espacio. Con eso podremos orientarte mejor desde el primer contacto.</p>
                <a class="button button--whatsapp button--large" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> {{ config('sentriq.contact.whatsapp_display') }}</a>
            </div>

            <div class="contact-details">
                <article><span>Horario</span><strong>{{ config('sentriq.contact.hours') }}</strong></article>
                <article><span>Correo</span><a href="mailto:{{ config('sentriq.contact.email') }}">{{ config('sentriq.contact.email') }}</a></article>
                <article><span>Cobertura</span><strong>Guadalajara y Zona Metropolitana</strong></article>
                <article><span>Facebook</span><a href="{{ config('sentriq.contact.facebook') }}" target="_blank" rel="noopener">sentriq.xytriq</a></article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container repair-card">
            <div class="service-card__icon"><x-icon name="shield" /></div>
            <div>
                <span class="eyebrow">Reparaciones y equipos de terceros</span>
                <h2>Visita de diagnóstico: $350 MXN</h2>
                <p>El importe se toma a cuenta si apruebas la reparación. Si decides no realizarla, recibirás el diagnóstico y la información necesaria para tomar una decisión.</p>
            </div>
            <a class="button button--outline" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">Agendar diagnóstico</a>
        </div>
    </section>
@endsection
