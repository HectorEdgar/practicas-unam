@extends('layouts.admin') 
@section('titulo') Editar Proyecto
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('ProyectoController@index')}}">Proyecto</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar proyecto {{$proyecto->proyecto}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($proyecto,['method'=>'PATCH','route'=>['proyecto.update',$proyecto->id_proyecto]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="proyecto">Proyecto</label>
            <input type="text" name="proyecto" class="form-control" placeholder="Proyecto..." value="{{$proyecto->proyecto}}">
        </div>
  
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('proyecto.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection