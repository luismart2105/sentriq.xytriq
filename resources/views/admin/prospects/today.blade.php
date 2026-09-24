@extends('admin.layout')
@section('title', 'Hoy')
@section('content')
    <div class="admin-heading"><div><span>Prioridades</span><h1>Hoy</h1><p>Nuevos sin atender y acciones que requieren seguimiento.</p></div><a class="admin-button" href="{{ route('admin.prospects.create') }}">Nuevo prospecto</a></div>
    @foreach ([['Nuevos sin atender', $newProspects], ['Seguimientos vencidos o de hoy', $followUps], ['Visitas pendientes', $visits], ['Cotizaciones por responder', $quotes]] as [$title, $items])
        <section class="today-section"><h2>{{ $title }} <span>{{ $items->count() }}</span></h2><div class="today-list">
            @forelse ($items as $prospect)<a href="{{ route('admin.prospects.show', $prospect) }}"><strong>{{ $prospect->displayName() }}</strong><span>{{ $prospect->next_follow_up_at?->format('d/m/Y H:i') ?: (\App\Models\Prospect::STAGES[$prospect->stage] ?? '') }}</span><small>{{ $prospect->phone ?: $prospect->email }}</small></a>@empty<p class="empty-panel">Nada pendiente en esta sección.</p>@endforelse
        </div></section>
    @endforeach
@endsection
