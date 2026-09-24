@extends('layouts.site')
@section('title', 'Aviso de privacidad')
@section('description', 'Aviso de privacidad para solicitudes de contacto de Sentriq.')
@section('robots', 'noindex, nofollow')
@section('content')
    <section class="page-hero"><div class="container page-hero__inner"><span class="eyebrow">Documento pendiente de aprobación</span><h1>Aviso de privacidad</h1><p>Versión provisional para revisión de Luisangel antes de habilitar el formulario.</p></div></section>
    <section class="section section--light"><div class="container legal-copy"><h2>Tratamiento de datos de contacto</h2><p>Sentriq recopilará los datos que envíes mediante el formulario exclusivamente para responder tu solicitud, dar seguimiento comercial y, cuando corresponda, preparar una visita o cotización.</p><p>Los datos pueden incluir nombre, teléfono, correo, municipio, servicio de interés y la descripción que decidas proporcionar. No se publicarán y su acceso se limitará al personal autorizado.</p><p>Para solicitar acceso, rectificación, cancelación u oposición, utiliza provisionalmente <a href="mailto:{{ config('sentriq.contact.email') }}">{{ config('sentriq.contact.email') }}</a>.</p><p><strong>Antes de producción:</strong> confirmar responsable legal, domicilio, mecanismos ARCO, transferencias, plazos de conservación y texto definitivo.</p></div></section>
@endsection
