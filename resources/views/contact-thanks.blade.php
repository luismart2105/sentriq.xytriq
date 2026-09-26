@extends('layouts.site')

@section('title', 'Solicitud recibida')
@section('description', 'Confirmación de solicitud recibida por Sentriq.')
@section('robots', 'noindex, follow')

@section('content')
    <section class="contact-confirmation">
        <div class="container contact-confirmation__inner">
            <div class="contact-confirmation__mark" aria-hidden="true">
                <x-icon name="check" />
            </div>
            <span class="eyebrow">Solicitud recibida</span>
            <h1>Gracias. El siguiente paso corre por nuestra cuenta.</h1>
            <p class="contact-confirmation__lead">Ya tenemos tu información. La revisaremos para entender lo que necesitas y preparar una orientación clara para tu proyecto o servicio.</p>

            <div class="contact-confirmation__steps" aria-label="Siguientes pasos">
                <article>
                    <span>1</span>
                    <strong>Solicitud registrada</strong>
                    <p>Tu información quedó guardada correctamente.</p>
                </article>
                <article>
                    <span>2</span>
                    <strong>Revisión del caso</strong>
                    <p>Revisaremos el servicio, la zona y los detalles que compartiste.</p>
                </article>
                <article>
                    <span>3</span>
                    <strong>Contacto personal</strong>
                    <p>Te responderemos dentro de 24 horas hábiles para definir cómo avanzar.</p>
                </article>
            </div>

            <p class="contact-confirmation__email">Si proporcionaste un correo electrónico, también recibirás ahí esta confirmación.</p>
            <div class="contact-confirmation__actions">
                <a class="button button--whatsapp" href="{{ config('sentriq.contact.whatsapp_url') }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> Agregar información por WhatsApp</a>
                <a class="button button--outline" href="{{ route('home') }}">Volver al inicio</a>
            </div>
        </div>
    </section>
@endsection
