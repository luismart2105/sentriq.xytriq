@extends('admin.layout')
@section('title', 'Nuevo presupuesto')
@section('content')
    <div class="admin-heading"><div><span>Presupuestos</span><h1>Nuevo presupuesto</h1></div></div>
    <form class="admin-form quote-editor" method="POST" action="{{ route('admin.quotes.store') }}">@csrf @include('admin.quotes.form')</form>
@endsection
