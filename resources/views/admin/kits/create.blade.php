@extends('admin.layout')

@section('title', 'Nuevo kit')

@section('content')
    <div class="admin-heading"><div><span>Catálogo</span><h1>Nuevo kit</h1></div></div>
    <form class="admin-form" method="POST" action="{{ route('admin.kits.store') }}">
        @csrf
        @include('admin.kits.form')
    </form>
@endsection
