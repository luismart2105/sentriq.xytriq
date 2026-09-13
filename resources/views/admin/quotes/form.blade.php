@php
    $items = old('items', $quote->items ?: [['concept' => '', 'model' => '', 'benefit' => '', 'quantity' => 1, 'unit_price' => 0]]);
@endphp
<section class="form-section">
    <div class="form-section__heading"><span>01</span><div><h2>Datos generales</h2><p>Información que aparecerá en el encabezado.</p></div></div>
    <div class="admin-form-grid">
        <label><span>Folio automático</span><input value="{{ $quote->number }}" readonly aria-describedby="folio-help"><small id="folio-help" class="form-help">Se asignará el siguiente consecutivo disponible al guardar.</small></label>
        <label><span>Cliente</span><input name="client_name" value="{{ old('client_name', $quote->client_name) }}" placeholder="Nombre o razón social" required>@error('client_name')<em>{{ $message }}</em>@enderror</label>
        <label><span>Fecha</span><input type="date" name="quote_date" value="{{ old('quote_date', optional($quote->quote_date)->format('Y-m-d')) }}" required></label>
        <label><span>Vigencia (días naturales)</span><input type="number" name="validity_days" min="1" max="365" value="{{ old('validity_days', $quote->validity_days) }}" required></label>
        <label class="admin-form-full"><span>Título</span><input name="title" value="{{ old('title', $quote->title) }}" required></label>
        <label class="admin-form-full"><span>Descripción</span><textarea name="description" rows="2">{{ old('description', $quote->description) }}</textarea></label>
        <label><span>Estado</span><select name="status"><option value="draft" @selected(old('status', $quote->status) === 'draft')>Borrador</option><option value="sent" @selected(old('status', $quote->status) === 'sent')>Enviado</option><option value="accepted" @selected(old('status', $quote->status) === 'accepted')>Aceptado</option></select></label>
        <label><span>Responsable del proyecto</span><input name="project_manager" value="{{ old('project_manager', $quote->project_manager) }}"></label>
        <div class="signature-editor admin-form-full" data-signature-pad>
            <div class="signature-editor__heading"><div><strong>Firma electrónica del responsable</strong><small>Dibuja con el mouse, el dedo o un stylus. La firma nueva sustituirá a la actual.</small></div><button type="button" data-clear-signature>Limpiar</button></div>
            <div class="signature-canvas-wrap"><canvas data-signature-canvas aria-label="Área para dibujar la firma"></canvas><span>Firma aquí</span></div>
            <input type="hidden" name="signature_data" data-signature-data value="">
            @error('signature_data')<em>{{ $message }}</em>@enderror
            @if ($quote->signatureUrl())
                <div class="signature-existing"><div class="signature-preview"><span>Firma guardada actualmente</span><img src="{{ $quote->signatureUrl() }}" alt="Firma de {{ $quote->project_manager ?: 'responsable del proyecto' }}"></div><label class="check-control"><input type="checkbox" name="remove_signature" value="1" @checked(old('remove_signature'))><span>Quitar la firma guardada</span></label></div>
            @endif
        </div>
    </div>
</section>

<section class="form-section" data-quote-items>
    <div class="form-section__heading"><span>02</span><div><h2>Equipos y materiales</h2><p>Los importes se calculan automáticamente.</p></div></div>
    @error('items')<em>{{ $message }}</em>@enderror
    <div class="quote-items">
        @foreach ($items as $index => $item)
            <article class="quote-item" data-item>
                <div class="quote-item__bar"><strong>Partida <span data-item-number>{{ $loop->iteration }}</span></strong><button type="button" data-remove-item>Quitar</button></div>
                <div class="quote-item__grid">
                    <label><span>Concepto</span><input name="items[{{ $index }}][concept]" value="{{ $item['concept'] ?? '' }}" required></label>
                    <label><span>Marca / modelo</span><input name="items[{{ $index }}][model]" value="{{ $item['model'] ?? '' }}"></label>
                    <label class="item-benefit"><span>Beneficio para el cliente</span><textarea name="items[{{ $index }}][benefit]" rows="2">{{ $item['benefit'] ?? '' }}</textarea></label>
                    <label><span>Cantidad</span><input data-quantity type="number" name="items[{{ $index }}][quantity]" min="0.01" step="0.01" value="{{ $item['quantity'] ?? 1 }}" required></label>
                    <label><span>Precio unitario</span><input data-price type="number" name="items[{{ $index }}][unit_price]" min="0" step="0.01" value="{{ $item['unit_price'] ?? 0 }}" required></label>
                    <div class="item-total"><span>Importe</span><strong data-line-total>$0.00</strong></div>
                </div>
            </article>
        @endforeach
    </div>
    <button class="admin-button admin-button--secondary" type="button" data-add-item>+ Agregar partida</button>
    <div class="quote-calculation"><span>Subtotal materiales <strong data-materials-total>$0.00</strong></span><label><span>Instalación y puesta en marcha</span><input data-installation type="number" name="installation_amount" min="0" step="0.01" value="{{ old('installation_amount', $quote->installation_amount ?? 0) }}"></label><span class="quote-calculation__total">Total del proyecto <strong data-grand-total>$0.00 MXN</strong></span><label class="quote-calculation__deposit"><span>Anticipo requerido</span><input data-deposit type="number" name="deposit_amount" min="0" step="0.01" value="{{ old('deposit_amount', $quote->deposit_amount ?? 0) }}"></label>@error('deposit_amount')<em class="quote-calculation__error">{{ $message }}</em>@enderror<span>Saldo restante <strong data-remaining-balance>$0.00</strong></span></div>
</section>

<section class="form-section">
    <div class="form-section__heading"><span>03</span><div><h2>Alcance, garantía y condiciones</h2><p>Usa una línea por punto en alcance y consideraciones.</p></div></div>
    <div class="admin-form-grid">
        <div class="warranty-period">
            <label><span>Duración — equipos</span><input type="number" name="equipment_warranty_duration" min="0" max="1200" value="{{ old('equipment_warranty_duration', $quote->equipment_warranty_duration ?? 1) }}" required></label>
            <label><span>Unidad</span><select name="equipment_warranty_unit"><option value="years" @selected(old('equipment_warranty_unit', $quote->equipment_warranty_unit ?? 'years') === 'years')>Años</option><option value="months" @selected(old('equipment_warranty_unit', $quote->equipment_warranty_unit ?? 'years') === 'months')>Meses</option></select></label>
            <small>Usa 0 para indicar que no hay garantía y explica el motivo abajo.</small>
        </div>
        <label><span>Descripción de garantía en equipos</span><textarea name="equipment_warranty" rows="5">{{ old('equipment_warranty', $quote->equipment_warranty) }}</textarea>@error('equipment_warranty')<em>{{ $message }}</em>@enderror</label>
        <div class="warranty-period">
            <label><span>Duración — instalación</span><input type="number" name="installation_warranty_duration" min="0" max="1200" value="{{ old('installation_warranty_duration', $quote->installation_warranty_duration ?? 2) }}" required></label>
            <label><span>Unidad</span><select name="installation_warranty_unit"><option value="years" @selected(old('installation_warranty_unit', $quote->installation_warranty_unit ?? 'years') === 'years')>Años</option><option value="months" @selected(old('installation_warranty_unit', $quote->installation_warranty_unit ?? 'years') === 'months')>Meses</option></select></label>
            <small>Usa 0 para indicar que no hay garantía y explica el motivo abajo.</small>
        </div>
        <label><span>Descripción de garantía en instalación</span><textarea name="installation_warranty" rows="5">{{ old('installation_warranty', $quote->installation_warranty) }}</textarea>@error('installation_warranty')<em>{{ $message }}</em>@enderror</label>
        <label class="admin-form-full"><span>Qué incluye la instalación <small>(un punto por línea)</small></span><textarea name="installation_scope" rows="6">{{ old('installation_scope', $quote->installation_scope) }}</textarea></label>
        <label class="admin-form-full"><span>Consideraciones del proyecto <small>(un punto por línea)</small></span><textarea name="project_considerations" rows="6">{{ old('project_considerations', $quote->project_considerations) }}</textarea></label>
    </div>
</section>
<div class="form-actions form-actions--sticky"><button class="admin-button" type="submit">Guardar presupuesto</button><a href="{{ route('admin.quotes.index') }}">Cancelar</a></div>

<template data-item-template>
    <article class="quote-item" data-item><div class="quote-item__bar"><strong>Partida <span data-item-number></span></strong><button type="button" data-remove-item>Quitar</button></div><div class="quote-item__grid"><label><span>Concepto</span><input data-name="concept" required></label><label><span>Marca / modelo</span><input data-name="model"></label><label class="item-benefit"><span>Beneficio para el cliente</span><textarea data-name="benefit" rows="2"></textarea></label><label><span>Cantidad</span><input data-name="quantity" data-quantity type="number" min="0.01" step="0.01" value="1" required></label><label><span>Precio unitario</span><input data-name="unit_price" data-price type="number" min="0" step="0.01" value="0" required></label><div class="item-total"><span>Importe</span><strong data-line-total>$0.00</strong></div></div></article>
</template>
<script src="{{ asset('assets/js/quotes.js') }}?v={{ filemtime(public_path('assets/js/quotes.js')) }}" defer></script>
