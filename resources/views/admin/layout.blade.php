<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>@yield('title', 'Administración') | Sentriq</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    </head>
    <body>
        <header class="admin-header">
            <a href="{{ route('admin.dashboard') }}"><strong>SENTRIQ</strong><span>Administración</span></a>
            @auth
                <nav>
                    <a href="{{ route('admin.dashboard') }}">Resumen</a>
                    <a href="{{ route('admin.kits.index') }}">Kits</a>
                    <a href="{{ route('admin.reviews.index') }}">Reseñas</a>
                    <a href="{{ route('admin.profile.edit') }}">Mi cuenta</a>
                    <a href="{{ route('home') }}" target="_blank">Ver sitio</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit">Salir</button>
                    </form>
                </nav>
            @endauth
        </header>

        <main class="admin-main">
            @if (session('status'))
                <div class="admin-alert">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </body>
</html>
