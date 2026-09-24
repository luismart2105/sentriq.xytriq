@extends('admin.layout')
@section('title', 'Nuevo prospecto')
@section('content')
    <div class="admin-heading"><div><span>Comercial</span><h1>Nuevo prospecto</h1></div></div>
    <form class="admin-form" method="POST" action="{{ route('admin.prospects.store') }}">@csrf @include('admin.prospects.form')</form>
@endsection
