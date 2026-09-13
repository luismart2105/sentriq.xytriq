<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $quote->number }} | Sentriq</title>
    <link rel="icon" href="{{ asset('assets/brand/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/quote.css') }}?v={{ filemtime(public_path('assets/css/quote.css')) }}">
</head>
<body>
    <div class="print-toolbar"><a href="{{ ($publicMode ?? false) ? route('quotes.sign', $quote->signing_token) : route('admin.quotes.edit', $quote) }}">← {{ ($publicMode ?? false) ? 'Volver a firma' : 'Editar' }}</a><span>Revisa la vista previa y selecciona “Guardar como PDF”.</span><button onclick="window.print()">Imprimir / Guardar PDF</button></div>
    <main class="document">
        <section class="page page-one">
            <header class="brand-header"><img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="Sentriq"><div>Seguridad inteligente para tus espacios<br><strong>sentriq.xytriq.com</strong></div></header>
            <div class="blue-rule"></div>
            <h1>{{ $quote->title }}</h1>
            @if ($quote->description)<p class="lead">{{ $quote->description }}</p>@endif
            <dl class="metadata"><dt>Presupuesto</dt><dd>{{ $quote->number }}</dd><dt>Fecha</dt><dd>{{ $quote->quote_date->locale('es')->translatedFormat('j \d\e F \d\e Y') }}</dd><dt>Cliente</dt><dd>{{ $quote->client_name }}</dd><dt>Vigencia</dt><dd>{{ $quote->validity_days }} días naturales</dd></dl>
            <h2>Equipos y materiales incluidos</h2>
            <table class="items-table">
                <thead><tr><th>Concepto</th><th>Beneficio para el cliente</th><th>Cant.</th><th>P. unit.</th><th>Importe</th></tr></thead>
                <tbody>@foreach ($quote->items as $item)<tr><td><strong>{{ $item['concept'] }}</strong>@if($item['model'])<small>{{ $item['model'] }}</small>@endif</td><td>{{ $item['benefit'] }}</td><td>{{ number_format((float) $item['quantity'], (float) $item['quantity'] == floor((float) $item['quantity']) ? 0 : 2) }}</td><td>${{ number_format((float) $item['unit_price'], 2) }}</td><td>${{ number_format((float) $item['quantity'] * (float) $item['unit_price'], 2) }}</td></tr>@endforeach</tbody>
            </table>
            <div class="totals"><div><span>Subtotal de materiales</span><strong>${{ number_format($quote->materialsSubtotal(), 2) }}</strong></div><div><span>Instalación, configuración y puesta en marcha</span><strong>${{ number_format((float) $quote->installation_amount, 2) }}</strong></div><div class="grand-total"><span>Total del proyecto</span><strong>${{ number_format($quote->total(), 2) }} MXN</strong></div><div class="deposit-total"><span>Anticipo requerido</span><strong>${{ number_format((float) $quote->deposit_amount, 2) }} MXN</strong></div><div><span>Saldo restante</span><strong>${{ number_format($quote->remainingBalance(), 2) }} MXN</strong></div></div>
            <p class="currency-note">Importes expresados en pesos mexicanos. El precio considera las cantidades y el alcance descritos en este documento.</p>
            <footer><span>SENTRIQ | Cámaras · Accesos · Automatización · Alarmas</span><span>Página 1</span></footer>
        </section>
        <section class="page page-two">
            <header class="brand-header brand-header--compact"><img src="{{ asset('assets/brand/FullLogo_Transparent_NoBuffer.png') }}" alt="Sentriq"><div>Seguridad inteligente para tus espacios<br><strong>sentriq.xytriq.com</strong></div></header>
            <h1>Alcance, garantía y condiciones</h1>
            @if($quote->description)<p class="lead">{{ $quote->description }}</p>@endif
            @if($quote->equipment_warranty)<article class="warranty warranty--equipment"><h3>{{ $quote->warrantyLabel('equipment') }} en equipos</h3><p>{{ $quote->equipment_warranty }}</p></article>@endif
            @if($quote->installation_warranty)<article class="warranty warranty--installation"><h3>{{ $quote->warrantyLabel('installation') }} en la instalación</h3><p>{{ $quote->installation_warranty }}</p></article>@endif
            @php
                $scope = array_filter(preg_split('/\r\n|\r|\n/', $quote->installation_scope ?? ''));
                $considerations = array_filter(preg_split('/\r\n|\r|\n/', $quote->project_considerations ?? ''));
            @endphp
            @if($scope)<h2>Qué incluye la instalación</h2><ul>@foreach($scope as $line)<li>{{ preg_replace('/^[•\-\s]+/u', '', $line) }}</li>@endforeach</ul>@endif
            @if($considerations)<h2>Consideraciones del proyecto</h2><ul>@foreach($considerations as $line)<li>{{ preg_replace('/^[•\-\s]+/u', '', $line) }}</li>@endforeach</ul>@endif
            <div class="investment"><span>Inversión total</span><strong>${{ number_format($quote->total(), 2) }} MXN</strong></div>
            <div class="deposit-summary"><span>Anticipo requerido <strong>${{ number_format((float) $quote->deposit_amount, 2) }} MXN</strong></span><span>Saldo restante <strong>${{ number_format($quote->remainingBalance(), 2) }} MXN</strong></span></div>
            <div class="signatures"><div class="signed-by-client"><span>Autoriza el cliente</span>@if($quote->clientSignatureUrl())<img src="{{ $quote->clientSignatureUrl() }}" alt="Firma del cliente">@endif<strong>{{ $quote->client_signer_name ?: 'Nombre y firma' }}</strong></div><div class="signed-by-sentriq"><span>Sentriq</span>@if($quote->signatureUrl())<img src="{{ $quote->signatureUrl() }}" alt="Firma del responsable">@endif<strong>{{ $quote->project_manager ?: 'Responsable del proyecto' }}</strong></div></div>
            <footer><span>SENTRIQ | Cámaras · Accesos · Automatización · Alarmas</span><span>Página 2</span></footer>
        </section>
    </main>
</body>
</html>
