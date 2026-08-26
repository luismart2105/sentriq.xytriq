@extends('admin.layout')

@section('title', 'Ingresar')

@section('content')
    <section class="login-card">
        <span>Acceso privado</span>
        <h1>Administración de Sentriq</h1>
        <p>Ingresa para actualizar kits y moderar reseñas.</p>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <label>
                <span>Correo</span>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <em>{{ $message }}</em> @enderror
            </label>
            <label>
                <span>Contraseña</span>
                <input type="password" name="password" autocomplete="current-password" required>
                @error('password') <em>{{ $message }}</em> @enderror
            </label>
            <label class="check-control">
                <input type="checkbox" name="remember" value="1">
                <span>Mantener sesión iniciada</span>
            </label>
            <button class="admin-button" type="submit">Ingresar</button>
        </form>
    </section>
@endsection
