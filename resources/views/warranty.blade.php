@extends('layouts.site')

@section('title', 'Garantías')
@section('description', 'Sentriq respalda sus instalaciones durante 2 años y los equipos durante 1 año contra defectos de fábrica.')

@section('content')
    <section class="page-hero">
        <div class="container page-hero__inner">
            <span class="eyebrow">Trabajo respaldado</span>
            <h1>Una garantía clara para tu tranquilidad</h1>
            <p>Confiamos en que una instalación realizada correctamente debe seguir funcionando de forma segura después de la entrega.</p>
        </div>
    </section>

    <section class="section section--light">
        <div class="container warranty-page-grid">
            <article class="warranty-card warranty-card--primary">
                <span class="big-number">2</span>
                <p class="warranty-card__unit">años</p>
                <h2>Garantía en instalación</h2>
                <p>Cubre defectos derivados del trabajo de instalación realizado por Sentriq durante dos años.</p>
                <ul class="check-list">
                    <li><x-icon name="check" /> Conexiones y terminaciones realizadas</li>
                    <li><x-icon name="check" /> Montaje de los elementos instalados</li>
                    <li><x-icon name="check" /> Corrección de defectos atribuibles al trabajo</li>
                </ul>
            </article>
            <article class="warranty-card">
                <span class="big-number">1</span>
                <p class="warranty-card__unit">año</p>
                <h2>Garantía en equipos</h2>
                <p>Cubre defectos de fábrica de los equipos conforme a las condiciones y validación de cada fabricante.</p>
                <ul class="check-list">
                    <li><x-icon name="check" /> Acompañamiento para gestionar la garantía</li>
                    <li><x-icon name="check" /> Revisión para identificar el origen de la falla</li>
                    <li><x-icon name="check" /> Condiciones explicadas desde la cotización</li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <div class="container fine-print">
            <h2>¿Qué necesitamos para atender una garantía?</h2>
            <p>Escríbenos con una descripción de la situación, fotografías o video cuando sea posible y los datos del proyecto. Revisaremos el caso para identificar si corresponde a instalación, configuración, equipo o una condición externa.</p>
            <p>Daños por manipulación de terceros, variaciones eléctricas, accidentes, modificaciones posteriores o condiciones ajenas a la instalación pueden requerir una valoración independiente.</p>
            <a class="button button--whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> Solicitar soporte</a>
        </div>
    </section>
@endsection
