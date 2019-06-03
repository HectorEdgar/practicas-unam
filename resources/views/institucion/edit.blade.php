@extends('layouts.admin')
@section('titulo') Editar ´Institucion
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('InstitucionController@index')}}">Institución</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Institución:{{$institucion->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="alert alert-info" role="alert">
                <strong>Sector Social</strong>: Se refiere a todas aquellas organizaciones no gubernamentales u organizaciones sociales
                <br>
                <strong>Sector Institucional/Gubernamental</strong>:Son todas aquellas dependencias gubernamentales, así como empresas e instituciones
                </div>
            </div>
        {!!Form::model($institucion,['method'=>'PATCH','route'=>['institucion.update',$institucion->Id_institucion]]) !!}
        {{Form::token()}}
       <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="nombre">Nombre de Institución</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Institución..." value="{{$institucion->nombre or ''}}"/>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="siglas">Siglas</label>
                    <input type="text" name="siglas" class="form-control" placeholder="Siglas..." value={{$institucion->siglas or ''}}>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
            <label for="pais">Ingresa un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
            <label class="input-group-text" for="pais">País</label>
            </div>

            <input type="text" name="pais" class="form-control"  value={{$institucion->pais or ''}}>
            </div>
        </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="sector">Elige un sector:</label><br>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                            <label class="input-group-text" for="sector">Sector</label>
                    </div>
                    <select class="custom-select" name="extra" id="extra">
                    @if($institucion->extra==2)
                    <option value="Sin Sector">Sin Sector</option>
                    <option value="Sector Social">Sector Social</option>
                    <option value="Sector Institucional/Gubernamental" selected>Sector Institucional/Gubernamental</option>
                    @elseif($institucion->extra==1)
                    <option value="Sin Sector">Sin Sector</option>
                    <option value="Sector Social" selected>Sector Social</option>
                    <option value="Sector Institucional/Gubernamental">Sector Institucional/Gubernamental</option>
                    @else
                    <option value="Sin Sector" selected>Sin Sector</option>
                    <option value="Sector Social">Sector Social</option>
                    <option value="Sector Institucional/Gubernamental">Sector Institucional/Gubernamental</option>
                    @endif
                </select>
                </div>
        </div>
        </div>

        <div class="form-group">
            <br><br>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('institucion.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection