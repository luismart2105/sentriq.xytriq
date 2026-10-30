@extends('admin.layout')

@section('title', 'Paquetes y soluciones')

@section('content')
    <div class="admin-heading">
        <div><span>Catálogo</span><h1>Paquetes y soluciones</h1></div>
        <a class="admin-button" href="{{ route('admin.kits.create') }}">Nueva opción</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Opción</th><th>Servicio</th><th>Precio</th><th>Estado</th><th>Orden</th><th></th></tr></thead>
            <tbody>
                @forelse ($kits as $kit)
                    <tr>
                        <td><strong>{{ $kit->name }}</strong><small>{{ $kit->camera_count ? $kit->camera_count.' cámaras' : 'Paquete o solución' }}</small></td>
                        <td>{{ $services[$kit->service_slug]['name'] ?? $kit->service_slug }}</td>
                        <td>
                            @if ($kit->pricing_type === 'quote')
                                Cotización
                            @else
                                {{ $kit->pricing_type === 'from' ? 'Desde ' : '' }}${{ number_format((float) $kit->price, 2) }}
                            @endif
                        </td>
                        <td><span @class(['status-pill', 'is-active' => $kit->active])>{{ $kit->active ? 'Publicado' : 'Borrador' }}</span></td>
                        <td>{{ $kit->sort_order }}</td>
                        <td class="table-actions">
                            <a href="{{ route('admin.kits.edit', $kit) }}">Editar</a>
                            <form method="POST" action="{{ route('admin.kits.destroy', $kit) }}" onsubmit="return confirm('¿Eliminar este kit?')">
                                @csrf @method('DELETE')
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">Todavía no hay opciones en el catálogo. Puedes crear paquetes con precio o soluciones que requieran valoración.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
