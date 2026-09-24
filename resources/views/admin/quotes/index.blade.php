@extends('admin.layout')

@section('title', 'Presupuestos')

@section('content')
    <div class="admin-heading">
        <div><span>Ventas</span><h1>Presupuestos</h1><p>Crea propuestas con el formato profesional de Sentriq.</p></div>
        <a class="admin-button" href="{{ route('admin.quotes.create') }}">Nuevo presupuesto</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Folio</th><th>Cliente</th><th>Prospecto</th><th>Fecha</th><th>Total</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse ($quotes as $quote)
                    <tr>
                        <td><strong>{{ $quote->number }}</strong><small>{{ $quote->title }}</small></td>
                        <td>{{ $quote->client_name }}</td>
                        <td>@if ($quote->prospect_id)<a href="{{ route('admin.prospects.show', $quote->prospect_id) }}">Ver ficha</a>@else—@endif</td>
                        <td>{{ $quote->quote_date->format('d/m/Y') }}</td>
                        <td><strong>${{ number_format($quote->total(), 2) }}</strong></td>
                        <td><span @class(['status-pill', 'is-active' => $quote->status === 'accepted', 'is-pending' => $quote->status === 'sent'])>{{ ['draft' => 'Borrador', 'sent' => 'Enviado', 'accepted' => 'Aceptado'][$quote->status] }}</span></td>
                        <td class="table-actions">
                            <a href="{{ route('admin.quotes.show', $quote) }}" target="_blank">Ver / PDF</a>
                            <a href="{{ route('admin.quotes.edit', $quote) }}">Editar</a>
                            <form method="POST" action="{{ route('admin.quotes.duplicate', $quote) }}">@csrf<button type="submit">Duplicar</button></form>
                            <form method="POST" action="{{ route('admin.quotes.destroy', $quote) }}" onsubmit="return confirm('¿Eliminar este presupuesto?')">@csrf @method('DELETE')<button type="submit">Eliminar</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-cell">Aún no hay presupuestos. Crea el primero usando el formato Sentriq.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
