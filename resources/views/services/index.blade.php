@extends('layouts.site')

@section('title', 'Servicios')
@section('description', 'Soluciones de videovigilancia, portones automáticos, control de acceso, alarmas, cercas eléctricas y acceso vehicular en Guadalajara.')

@section('content')
    <section class="page-hero">
        <div class="container page-hero__inner">
            <span class="eyebrow">Servicios de seguridad electrónica</span>
            <h1>Una solución adecuada para cada espacio</h1>
            <p>Analizamos necesidades, condiciones del inmueble y objetivos para recomendar sistemas funcionales, confiables y preparados para el uso diario.</p>
        </div>
    </section>

    <section class="section section--light">
        <div class="container">
            <div class="service-grid">
                @foreach ($services as $slug => $service)
                    <x-service-card :slug="$slug" :service="$service" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container repair-card">
            <div class="service-card__icon"><x-icon name="shield" /></div>
            <div>
                <span class="eyebrow">Servicio complementario</span>
                <h2>Diagnóstico y reparación</h2>
                <p>También revisamos equipos que no fueron instalados por Sentriq. La visita de diagnóstico tiene un costo de <strong>$350 MXN</strong>, que se toma a cuenta si apruebas la reparación. Si decides no realizarla, recibes el diagnóstico y las indicaciones necesarias.</p>
            </div>
            <a class="button button--outline" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener">Solicitar diagnóstico</a>
        </div>
    </section>
@endsection
