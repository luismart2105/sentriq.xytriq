@props(['slug', 'service'])

@php
    $icons = [
        'camaras-de-seguridad' => 'camera',
        'automatizacion-de-portones' => 'gate',
        'control-de-acceso' => 'access',
        'alarmas' => 'alarm',
        'cercas-electricas' => 'fence',
        'acceso-vehicular' => 'vehicle',
    ];
@endphp

<article class="service-card">
    <div class="service-card__icon"><x-icon :name="$icons[$slug] ?? 'shield'" /></div>
    @if ($service['priority'] === 'principal')
        <span class="service-card__tag">Servicio destacado</span>
    @endif
    <h3>{{ $service['name'] }}</h3>
    <p>{{ $service['summary'] }}</p>
    <a href="{{ route('services.show', $slug) }}">
        Conocer el servicio
        <x-icon name="arrow" />
    </a>
</article>
