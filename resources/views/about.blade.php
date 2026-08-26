@extends('layouts.site')

@section('title', 'Nosotros')
@section('description', 'Conoce la filosofía de Sentriq by Xytriq y nuestro compromiso con instalaciones de seguridad electrónica confiables.')

@section('content')
    <section class="page-hero">
        <div class="container page-hero__inner">
            <span class="eyebrow">Sentriq by Xytriq</span>
            <h1>Seguridad electrónica con atención personal</h1>
            <p>Somos un proyecto local de Guadalajara enfocado en crear soluciones funcionales y confiables para hogares, negocios y empresas.</p>
        </div>
    </section>

    <section class="section section--light">
        <div class="container story-grid">
            <div>
                <span class="eyebrow">Nuestra forma de trabajar</span>
                <h2>No instalamos por instalar</h2>
            </div>
            <div class="story-copy">
                <p>Cada espacio tiene accesos, riesgos y rutinas diferentes. Nuestro trabajo comienza por escuchar, revisar y explicar las opciones para que cada cliente pueda tomar una decisión informada.</p>
                <p>Confiamos en el cuidado con el que realizamos las instalaciones. Por eso ofrecemos dos años de garantía sobre nuestro trabajo y acompañamos al cliente después de entregar el proyecto.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading section-heading--center">
                <span class="eyebrow">El equipo</span>
                <h2>Las personas detrás de Sentriq</h2>
                <p>Estamos preparando los perfiles de nuestros tres colaboradores. Muy pronto podrás conocer su experiencia, funciones y certificaciones.</p>
            </div>
            <div class="team-grid">
                @foreach (range(1, 3) as $member)
                    <article class="team-card">
                        <div class="team-card__placeholder"><span>S{{ $member }}</span></div>
                        <h3>Perfil en preparación</h3>
                        <p>Nombre, cargo, experiencia y certificaciones.</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
