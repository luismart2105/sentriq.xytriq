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

    @if (config('sentriq.leads.form_enabled'))
        <section class="section" id="formulario">
            <div class="container lead-form-layout">
                <div><span class="eyebrow">Solicitud en línea</span><h2>Solicita que te contactemos</h2><p>Atendemos proyectos nuevos, soporte y reparaciones. Te responderemos dentro de 24 horas hábiles.</p><p>El levantamiento técnico, selección de equipos y precio se confirman después de revisar tu solicitud.</p></div>
                <form class="lead-form" method="POST" action="{{ route('contact.store') }}">@csrf
                    @if (session('contact_success'))<div class="form-success" role="status">{{ session('contact_success') }}</div>@endif
                    <div class="honeypot" aria-hidden="true"><label>No llenar<input name="website" tabindex="-1" autocomplete="off"></label></div>
                    <label><span>Nombre</span><input name="name" value="{{ old('name') }}" maxlength="160" autocomplete="name" required>@error('name')<em>{{ $message }}</em>@enderror</label>
                    <label><span>Tipo de solicitud</span><select name="request_type" required><option value="">Selecciona una opción</option>@foreach (\App\Models\Prospect::REQUEST_TYPES as $key => $label)<option value="{{ $key }}" @selected(old('request_type') === $key)>{{ $label }}</option>@endforeach</select>@error('request_type')<em>{{ $message }}</em>@enderror</label>
                    <div class="lead-form__row"><label><span>Teléfono</span><input name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel">@error('phone')<em>{{ $message }}</em>@enderror</label><label><span>Correo</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email">@error('email')<em>{{ $message }}</em>@enderror</label></div>
                    <div class="lead-form__row"><label><span>Servicio</span><select name="service_interest" required><option value="">Selecciona una opción</option>@foreach (config('sentriq.services') as $key => $service)<option value="{{ $key }}" @selected(old('service_interest') === $key)>{{ $service['name'] }}</option>@endforeach</select></label><label><span>Municipio o zona</span><input name="municipality" value="{{ old('municipality') }}" maxlength="120" required></label></div>
                    <label><span>Cuéntanos brevemente qué necesitas</span><textarea name="description" rows="5" maxlength="2000" required>{{ old('description') }}</textarea>@error('description')<em>{{ $message }}</em>@enderror</label>
                    @foreach ($campaign as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ old($key, $value) }}">@endforeach
                    <input type="hidden" name="landing_page" value="{{ old('landing_page', request()->fullUrl()) }}"><input type="hidden" name="referrer" value="{{ old('referrer', request()->headers->get('referer')) }}">
                    <label class="privacy-check"><input type="checkbox" name="privacy_accepted" value="1" @checked(old('privacy_accepted')) required><span>He leído y acepto el <a href="{{ route('privacy') }}" target="_blank">aviso de privacidad</a> para que Sentriq atienda mi solicitud.</span></label>@error('privacy_accepted')<em>{{ $message }}</em>@enderror
                    <button class="button button--primary" type="submit">Enviar solicitud</button>
                </form>
            </div>
        </section>
    @endif

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
