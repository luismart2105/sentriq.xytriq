@extends('admin.layout')

@section('title', 'Nueva opción del catálogo')

@section('content')
    <div class="admin-heading"><div><span>Catálogo</span><h1>Nueva opción</h1></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.kits.store') }}">
        @csrf
        @include('admin.kits.form')
    </form>
@endsection
