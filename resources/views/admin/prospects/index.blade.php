@extends('admin.layout')
@section('title', 'Prospectos')
@section('content')
    <div class="admin-heading">
        <div><span>Embudo comercial</span><h1>Prospectos</h1><p>Personas identificadas; los clics a WhatsApp se muestran aparte.</p></div>
        <div class="heading-actions"><a class="admin-button admin-button--secondary" href="{{ route('admin.prospects.today') }}">Ver hoy</a><a class="admin-button" href="{{ route('admin.prospects.create') }}">Nuevo prospecto</a></div>
    </div>

    <form class="filter-panel" method="GET">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nombre, negocio o teléfono">
        <select name="stage"><option value="">Todas las etapas</option>@foreach (\App\Models\Prospect::STAGES as $key => $label)<option value="{{ $key }}" @selected(($filters['stage'] ?? '') === $key)>{{ $label }}</option>@endforeach</select>
        <select name="source"><option value="">Todas las fuentes</option>@foreach (\App\Models\Prospect::SOURCES as $key => $label)<option value="{{ $key }}" @selected(($filters['source'] ?? '') === $key)>{{ $label }}</option>@endforeach</select>
        <select name="service"><option value="">Todos los servicios</option>@foreach (config('sentriq.services') as $key => $service)<option value="{{ $key }}" @selected(($filters['service'] ?? '') === $key)>{{ $service['name'] }}</option>@endforeach</select>
        <select name="follow_up"><option value="">Cualquier seguimiento</option><option value="due" @selected(($filters['follow_up'] ?? '') === 'due')>Vencido</option><option value="today" @selected(($filters['follow_up'] ?? '') === 'today')>Para hoy</option></select>
        <label>Desde<input type="date" name="from" value="{{ $filters['from'] ?? '' }}"></label><label>Hasta<input type="date" name="to" value="{{ $filters['to'] ?? '' }}"></label>
        <button class="admin-button" type="submit">Filtrar</button><a href="{{ route('admin.prospects.index') }}">Limpiar</a>
    </form>

    <section class="metrics-panel">
        <div><strong>Resultados por fuente</strong><span>{{ $periodLabel }}</span></div>
        <div class="metrics-table-wrap"><table class="metrics-table"><thead><tr><th>Fuente</th><th>Prospectos</th><th>Visitas</th><th>Cotizaciones</th><th>Ganados</th></tr></thead><tbody>
        @foreach (\App\Models\Prospect::SOURCES as $source => $label)
            @php $events = $stageMetrics->get($source, collect())->keyBy('new_stage'); @endphp
            <tr><td>{{ $label }}</td><td>{{ $sourceMetrics[$source] ?? 0 }}</td><td>{{ optional($events->get('visit'))->total ?? 0 }}</td><td>{{ optional($events->get('quote'))->total ?? 0 }}</td><td>{{ optional($events->get('won'))->total ?? 0 }}</td></tr>
        @endforeach
        </tbody></table></div>
        <p><strong>{{ $whatsappClicks }}</strong> clics a WhatsApp en el periodo. Son eventos anónimos, no conversaciones ni prospectos.</p>
    </section>

    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Prospecto</th><th>Servicio</th><th>Fuente</th><th>Etapa</th><th>Seguimiento</th><th></th></tr></thead><tbody>
        @forelse ($prospects as $prospect)
            <tr><td><strong>{{ $prospect->displayName() }}</strong><small>{{ $prospect->phone ?: $prospect->email }}</small></td><td>{{ config('sentriq.services.'.$prospect->service_interest.'.name', '—') }}</td><td>{{ \App\Models\Prospect::SOURCES[$prospect->source] }}</td><td><span class="status-pill {{ in_array($prospect->stage, ['won', 'interested']) ? 'is-active' : ($prospect->stage === 'new' ? 'is-pending' : '') }}">{{ \App\Models\Prospect::STAGES[$prospect->stage] }}</span></td><td @class(['is-overdue' => $prospect->next_follow_up_at?->isPast()])>{{ $prospect->next_follow_up_at?->format('d/m/Y H:i') ?: '—' }}</td><td class="table-actions"><a href="{{ route('admin.prospects.show', $prospect) }}">Abrir</a><a href="{{ route('admin.prospects.edit', $prospect) }}">Editar</a></td></tr>
        @empty <tr><td colspan="6" class="empty-cell">No hay prospectos con estos filtros.</td></tr> @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $prospects->links() }}</div>
@endsection
