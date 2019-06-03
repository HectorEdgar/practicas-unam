@extends('layouts.admin') 
@section('titulo') Editar Tema
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('TemaController@index')}}">Tema</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Tema {{$tema->descripcion}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($tema,['method'=>'PATCH','route'=>['tema.update',$tema->id_tema]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" name="descripcion" class="form-control" placeholder="Descripción..." value="{{$tema->descripcion}}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('tema.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection