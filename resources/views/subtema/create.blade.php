@extends('layouts.admin') 
@section('titulo')
    Crear Subtema
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('SubtemaController@index')}}">Subtema</a></li>
                <li class="breadcrumb-item active" aria-current="page">Agregar</li>
            </ol>
        </nav>
        <h3>Nuevo Subtema</h3>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 
        {!!Form::open(array('url'=>'/subtema','method'=>'POST','autocomplete'=>'off')) !!} {{Form::token()}}
        <div class="form-group">
            <label for="subtema">Subtema</label>
            <input type="text" name="subtema" class="form-control" placeholder="Subtema...">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('subtema.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection