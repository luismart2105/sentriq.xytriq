@extends('admin.layout')
@section('title', 'Editar presupuesto')
@section('content')
    <div class="admin-heading">
        <div><span>{{ $quote->number }}</span><h1>Editar presupuesto</h1></div>
        <a class="admin-button admin-button--secondary" href="{{ route('admin.quotes.show', $quote) }}" target="_blank">Vista previa / PDF</a>
    </div>
    <section class="signing-link-panel">
        <div><strong>Firma del cliente</strong><p>{{ $quote->signed_at ? 'Firmado por '.$quote->client_signer_name.' el '.$quote->signed_at->format('d/m/Y H:i').' h.' : 'Genera un enlace privado para que el cliente revise y firme desde su dispositivo.' }}</p></div>
        @if ($quote->signing_token && ! $quote->signed_at)
            <div class="signing-link-copy"><input value="{{ route('quotes.sign', $quote->signing_token) }}" readonly data-copy-value><button type="button" data-copy-button>Copiar enlace</button></div>
        @endif
        @if (! $quote->signed_at)
            <form method="POST" action="{{ route('admin.quotes.signing-link', $quote) }}" onsubmit="return {{ $quote->signing_token ? "confirm('El enlace anterior dejará de funcionar. ¿Generar uno nuevo?')" : 'true' }}">@csrf<button class="admin-button admin-button--secondary" type="submit">{{ $quote->signing_token ? 'Renovar enlace' : 'Generar enlace para firma' }}</button></form>
        @else
            <a class="admin-button admin-button--secondary" href="{{ route('admin.quotes.show', $quote) }}" target="_blank">Ver documento firmado</a>
        @endif
    </section>
    @if ($quote->signed_at)
        <section class="signed-lock-panel"><strong>Documento protegido</strong><p>Este presupuesto ya fue autorizado y no puede modificarse sin invalidar la evidencia. Puedes duplicarlo para crear una versión nueva.</p><form method="POST" action="{{ route('admin.quotes.duplicate', $quote) }}">@csrf<button class="admin-button" type="submit">Duplicar presupuesto</button></form></section>
    @else
        <form class="admin-form quote-editor" method="POST" action="{{ route('admin.quotes.update', $quote) }}">@csrf @method('PUT') @include('admin.quotes.form')</form>
    @endif
@endsection
