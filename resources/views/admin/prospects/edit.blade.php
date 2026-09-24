@extends('admin.layout')
@section('title', 'Editar prospecto')
@section('content')
    <div class="admin-heading"><div><span>{{ $prospect->displayName() }}</span><h1>Editar prospecto</h1></div><a class="admin-button admin-button--secondary" href="{{ route('admin.prospects.show', $prospect) }}">Volver a la ficha</a></div>
    <form class="admin-form" method="POST" action="{{ route('admin.prospects.update', $prospect) }}">@csrf @method('PUT') @include('admin.prospects.form')</form>
@endsection
