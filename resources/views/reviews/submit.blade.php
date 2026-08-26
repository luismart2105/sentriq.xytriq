@extends('layouts.site')

@section('title', 'Comparte tu experiencia')
@section('description', 'Formulario privado para clientes de Sentriq.')

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="container page-hero__inner">
            <span class="eyebrow">Invitación privada</span>
            <h1>¿Cómo fue tu experiencia con Sentriq?</h1>
            <p>Tu opinión nos ayuda a mejorar y permite que otras personas conozcan nuestra forma de trabajar.</p>
        </div>
    </section>

    <section class="section section--light">
        <div class="container review-form-wrap">
            @if (session('review_message'))
                <div class="alert alert--success">{{ session('review_message') }}</div>
            @endif

            @if ($review->status === 'invited')
                <form class="review-form" method="POST" action="{{ route('reviews.store', $review->token) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <label>
                            <span>Nombre</span>
                            <input type="text" name="client_name" value="{{ old('client_name') }}" maxlength="100" required>
                            <small>Publicaremos únicamente tu primer nombre.</small>
                            @error('client_name') <em>{{ $message }}</em> @enderror
                        </label>
                        <label>
                            <span>Municipio</span>
                            <input type="text" name="municipality" value="{{ old('municipality') }}" maxlength="100" placeholder="Ej. Zapopan" required>
                            @error('municipality') <em>{{ $message }}</em> @enderror
                        </label>
                    </div>

                    <label>
                        <span>Servicio contratado</span>
                        <input type="text" name="service" value="{{ old('service') }}" maxlength="150" placeholder="Ej. Instalación de cámaras" required>
                        @error('service') <em>{{ $message }}</em> @enderror
                    </label>

                    <fieldset class="rating-field">
                        <legend>Calificación</legend>
                        <div>
                            @foreach (range(5, 1) as $rating)
                                <input id="rating-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                                <label for="rating-{{ $rating }}" aria-label="{{ $rating }} estrellas">★</label>
                            @endforeach
                        </div>
                        @error('rating') <em>{{ $message }}</em> @enderror
                    </fieldset>

                    <label>
                        <span>Cuéntanos tu experiencia</span>
                        <textarea name="comment" rows="6" minlength="20" maxlength="1500" required>{{ old('comment') }}</textarea>
                        @error('comment') <em>{{ $message }}</em> @enderror
                    </label>

                    <label>
                        <span>Fotografía <small>(opcional)</small></span>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
                        <small>Máximo 4 MB. No publiques rostros, placas o datos que no quieras mostrar.</small>
                        @error('photo') <em>{{ $message }}</em> @enderror
                    </label>

                    <p class="form-privacy">Al enviar esta reseña autorizas a Sentriq a mostrar tu primer nombre, municipio, servicio, calificación y comentario después de una revisión.</p>

                    <button class="button button--whatsapp" type="submit">Enviar reseña</button>
                </form>
            @else
                <div class="review-form review-form--complete">
                    <x-icon name="check" />
                    <h2>Esta invitación ya fue utilizada</h2>
                    <p>Gracias por dedicar tiempo a compartir tu experiencia.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
