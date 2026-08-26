@extends('admin.layout')

@section('title', 'Reseñas')

@section('content')
    <div class="admin-heading">
        <div><span>Confianza</span><h1>Reseñas</h1></div>
        <form method="POST" action="{{ route('admin.reviews.invite') }}">
            @csrf
            <button class="admin-button" type="submit">Crear enlace privado</button>
        </form>
    </div>

    @if (session('invite_url'))
        <div class="invite-box">
            <strong>Comparte este enlace únicamente con el cliente:</strong>
            <div><input id="invite-url" type="text" value="{{ session('invite_url') }}" readonly><button type="button" onclick="navigator.clipboard.writeText(document.getElementById('invite-url').value)">Copiar</button></div>
        </div>
    @endif

    <div class="review-admin-list">
        @forelse ($reviews as $review)
            <article class="review-admin-card">
                <div class="review-admin-card__top">
                    <span @class(['status-pill', 'is-active' => $review->status === 'approved', 'is-pending' => $review->status === 'pending'])>
                        {{ match($review->status) { 'invited' => 'Invitación creada', 'pending' => 'Pendiente', 'approved' => 'Publicada', 'rejected' => 'Rechazada', default => $review->status } }}
                    </span>
                    <small>{{ $review->created_at->format('d/m/Y') }}</small>
                </div>
                @if ($review->status === 'invited')
                    <p>Enlace sin utilizar:</p>
                    <code>{{ route('reviews.show', $review->token) }}</code>
                @else
                    <div class="review-admin-card__rating">{{ str_repeat('★', (int) $review->rating) }}</div>
                    <h2>{{ $review->client_name }} · {{ $review->municipality }}</h2>
                    <small>{{ $review->service }}</small>
                    <blockquote>{{ $review->comment }}</blockquote>
                    @if ($review->photo_path)
                        <a href="{{ asset('storage/'.$review->photo_path) }}" target="_blank" rel="noopener">Ver fotografía adjunta</a>
                    @endif
                    <div class="review-actions">
                        @foreach (['approved' => 'Aprobar', 'rejected' => 'Rechazar', 'pending' => 'Dejar pendiente'] as $status => $label)
                            <form method="POST" action="{{ route('admin.reviews.status', $review) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button type="submit">{{ $label }}</button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="empty-panel">Aún no hay invitaciones ni reseñas.</div>
        @endforelse
    </div>
@endsection
