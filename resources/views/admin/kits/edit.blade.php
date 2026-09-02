@extends('admin.layout')

@section('title', 'Editar kit')

@section('content')
    <div class="admin-heading"><div><span>Catálogo</span><h1>Editar {{ $kit->name }}</h1></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.kits.update', $kit) }}">
        @csrf @method('PUT')
        @include('admin.kits.form')
    </form>
@endsection
