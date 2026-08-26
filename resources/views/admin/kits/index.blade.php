@extends('admin.layout')

@section('title', 'Kits')

@section('content')
    <div class="admin-heading">
        <div><span>Catálogo</span><h1>Kits de cámaras</h1></div>
        <a class="admin-button" href="{{ route('admin.kits.create') }}">Nuevo kit</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Kit</th><th>Precio</th><th>Estado</th><th>Orden</th><th></th></tr></thead>
            <tbody>
                @forelse ($kits as $kit)
                    <tr>
                        <td><strong>{{ $kit->name }}</strong><small>{{ $kit->camera_count ? $kit->camera_count.' cámaras' : 'Personalizado' }}</small></td>
                        <td>${{ number_format((float) $kit->price, 2) }}</td>
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
                    <tr><td colspan="5" class="empty-cell">Todavía no hay kits. Crea el primero cuando tengas definidos precio y características.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
