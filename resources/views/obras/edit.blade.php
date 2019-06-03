@extends('layouts.admin') 
@section('titulo') Editar Obra
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('ObraController@index')}}">Obra</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Obra {{$obra->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($obra,['method'=>'PATCH','route'=>['obras.update',$obra->id_obra]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre..." value="{{$obra->nombre}}">
        </div>

        <div class="form-group">
            <div class="alert alert-info" role="alert">
                <strong>Especifíque:</strong> Esta Infraestructura se trata de:
                <select class="custom-select" name="tipo" id="tipo">
                    @if ($obra->extra==1)
                    <option value="obra" selected>Obra</option>
                    <option value="complejo">Complejo</option>
                    @endif
                    @if ($obra->extra==2)
                    <option value="obra">Obra</option>
                    <option value="complejo" selected>Complejo</option>
                    @endif
                </select>
            </div>
         </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('obras.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection