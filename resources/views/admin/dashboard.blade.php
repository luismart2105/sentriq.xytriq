@extends('admin.layout')

@section('title', 'Resumen')

@section('content')
    <div class="admin-heading">
        <div><span>Panel</span><h1>Resumen</h1></div>
        <p>Hola, {{ auth()->user()->name }}.</p>
    </div>

    <div class="stat-grid">
        <article><span>Kits</span><strong>{{ $kitCount }}</strong><small>{{ $activeKitCount }} publicados</small></article>
        <article><span>Reseñas pendientes</span><strong>{{ $pendingReviewCount }}</strong><small>por revisar</small></article>
        <article><span>Reseñas publicadas</span><strong>{{ $approvedReviewCount }}</strong><small>visibles en el sitio</small></article>
    </div>

    <div class="admin-actions">
        <a class="admin-card-link" href="{{ route('admin.kits.create') }}"><strong>Crear un kit</strong><span>Agrega precio, características y disponibilidad.</span></a>
        <a class="admin-card-link" href="{{ route('admin.reviews.index') }}"><strong>Invitar a dejar una reseña</strong><span>Genera un enlace privado para un cliente.</span></a>
    </div>
@endsection
