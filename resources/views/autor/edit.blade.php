@extends('layouts.admin') 
@section('titulo') Editar Autor
@endsection
@section('contenido')
@if(Session::has('Aviso'))
<div class="alert alert-warning">
  <strong>¡Éxito!</strong> {{Session::get('Aviso')}}
</div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('AutorController@index')}}">Autor</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Autor {{$autor->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($autor,['method'=>'PATCH','route'=>['autor.update',$autor->Id_autor]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="pseudonimo">Pseudónimo</label>
            <input type="text" name="pseudonimo" class="form-control" placeholder="Pseudónimo..." value="{{$autor->pseudonimo}}">
        </div>
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre..." value="{{$autor->nombre}}">
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos..." value="{{$autor->apellidos}}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('autor.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection