<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow"><meta name="referrer" content="no-referrer">
    <title>Firmar {{ $quote->number }} | Sentriq</title>
    <link rel="icon" href="{{ asset('assets/brand/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/client-signature.css') }}">
</head>
<body>
    <header class="client-header"><img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="Sentriq"><span>Autorización de presupuesto</span></header>
    <main>
        @if (session('status'))<div class="success-message"><strong>Firma completada</strong><p>{{ session('status') }}</p></div>@endif
        <section class="quote-summary">
            <div class="summary-heading"><div><small>{{ $quote->number }}</small><h1>{{ $quote->title }}</h1><p>Preparado para {{ $quote->client_name }}</p></div><div class="summary-total"><span>Inversión total</span><strong>${{ number_format($quote->total(), 2) }} MXN</strong></div></div>
            <dl><dt>Fecha</dt><dd>{{ $quote->quote_date->format('d/m/Y') }}</dd><dt>Vigencia</dt><dd>{{ $quote->validity_days }} días naturales</dd></dl>
            <div class="client-items"><table><thead><tr><th>Concepto</th><th>Cantidad</th><th>Importe</th></tr></thead><tbody>@foreach($quote->items as $item)<tr><td><strong>{{ $item['concept'] }}</strong>@if($item['model'])<small>{{ $item['model'] }}</small>@endif</td><td>{{ number_format((float)$item['quantity'], (float)$item['quantity'] == floor((float)$item['quantity']) ? 0 : 2) }}</td><td>${{ number_format((float)$item['quantity'] * (float)$item['unit_price'], 2) }}</td></tr>@endforeach<tr><td>Instalación, configuración y puesta en marcha</td><td>—</td><td>${{ number_format((float)$quote->installation_amount, 2) }}</td></tr></tbody></table></div>
            <a class="document-link" href="{{ route('quotes.document', $quote->signing_token) }}" target="_blank">Ver presupuesto completo</a>
        </section>

        @if ($quote->signed_at)
            <section class="already-signed"><span>Presupuesto autorizado</span><h2>Firmado por {{ $quote->client_signer_name }}</h2><p>{{ $quote->signed_at->locale('es')->translatedFormat('j \d\e F \d\e Y, H:i') }} h</p>@if($quote->clientSignatureUrl())<img src="{{ $quote->clientSignatureUrl() }}" alt="Firma registrada">@endif</section>
        @else
            <form method="POST" action="{{ route('quotes.sign.store', $quote->signing_token) }}" class="client-sign-form" data-signature-pad>
                @csrf
                <span class="step-label">Autorización</span><h2>Firma del cliente</h2><p>Al firmar confirmas que revisaste y autorizas el presupuesto mostrado.</p>
                <label><span>Nombre completo de quien autoriza</span><input name="client_signer_name" value="{{ old('client_signer_name', $quote->client_name) }}" required autocomplete="name">@error('client_signer_name')<em>{{ $message }}</em>@enderror</label>
                <div class="signature-canvas-wrap"><canvas data-signature-canvas aria-label="Área para dibujar la firma"></canvas><span>Firma aquí</span></div>
                <div class="pad-actions"><button type="button" data-clear-signature>Limpiar firma</button></div>
                <input type="hidden" name="signature_data" data-signature-data>@error('signature_data')<em>{{ $message }}</em>@enderror
                <label class="acceptance"><input type="checkbox" name="acceptance" value="1" required><span>Acepto que esta firma electrónica representa mi autorización del presupuesto {{ $quote->number }} por un total de ${{ number_format($quote->total(), 2) }} MXN.</span></label>@error('acceptance')<em>{{ $message }}</em>@enderror
                <button class="sign-submit" type="submit">Firmar y autorizar presupuesto</button>
                <small class="security-note">La fecha, hora y datos técnicos de esta autorización se registrarán como evidencia.</small>
            </form>
        @endif
    </main>
    <footer>Sentriq · Seguridad inteligente para tus espacios</footer>
    <script src="{{ asset('assets/js/quotes.js') }}?v={{ filemtime(public_path('assets/js/quotes.js')) }}" defer></script>
</body>
</html>
