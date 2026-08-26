@extends('admin.layout')

@section('title', 'Mi cuenta')

@section('content')
    <div class="admin-heading">
        <div><span>Seguridad</span><h1>Mi cuenta</h1></div>
    </div>

    <form class="admin-form admin-form--narrow" method="POST" action="{{ route('admin.profile.update') }}">
        @csrf @method('PUT')

        <div class="admin-form-grid">
            <label class="admin-form-full">
                <span>Nombre</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required>
                @error('name') <em>{{ $message }}</em> @enderror
            </label>
            <label class="admin-form-full">
                <span>Correo de acceso</span>
                <input type="email" value="{{ $user->email }}" disabled>
            </label>
            <label class="admin-form-full">
                <span>Contraseña actual</span>
                <input type="password" name="current_password" autocomplete="current-password" required>
                @error('current_password') <em>{{ $message }}</em> @enderror
            </label>
            <label>
                <span>Nueva contraseña</span>
                <input type="password" name="password" autocomplete="new-password" required>
                @error('password') <em>{{ $message }}</em> @enderror
            </label>
            <label>
                <span>Confirmar nueva contraseña</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>
        </div>

        <p class="form-help">Usa al menos 10 caracteres con mayúsculas, minúsculas y números.</p>
        <button class="admin-button" type="submit">Actualizar contraseña</button>
    </form>
@endsection
