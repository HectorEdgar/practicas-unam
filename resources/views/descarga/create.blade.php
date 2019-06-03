@extends('layouts.admin') 
@section('titulo')
    Crear Descarga
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('DescargaController@index')}}">Descarga</a></li>
                <li class="breadcrumb-item active" aria-current="page">Agregar</li>
            </ol>
        </nav>
        <h3>Nuevo Descarga</h3>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 
        {!!Form::open(array('url'=>'/descarga','method'=>'POST','autocomplete'=>'off','files'=>'true')) !!} {{Form::token()}}
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" class="form-control" placeholder="Título...">
        </div>
        <div class="form-group">
            <label for="url">Url</label>
            <input type="text" name="url" class="form-control" placeholder="Url...">
        </div>
        <div class="form-group">
            <label for="tipoProyecto">Proyecto</label>
            <input type="text" name="tipoProyecto" class="form-control" placeholder="Proyecto...">
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" name="estado">
                <option value="Catalogado">Catalogado</option>
                <option selected value="No Catalogado">No Catalogado</option>
            </select>
        </div>
       
        <div class="form-group">
            <label for="archivo">Archivo</label>
            <input type="file" name="archivo" required class="form-control">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('descarga.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection