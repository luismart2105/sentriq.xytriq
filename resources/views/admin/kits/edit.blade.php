@extends('admin.layout')

@section('title', 'Editar opción')

@section('content')
    <div class="admin-heading"><div><span>Catálogo</span><h1>Editar opción</h1><p>{{ $kit->name }}</p></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.kits.update', $kit) }}">
        @csrf @method('PUT')
        @include('admin.kits.form')
    </form>
@endsection
