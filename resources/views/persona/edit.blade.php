@extends('layouts.admin') 
@section('titulo') Editar ´Persona
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/paises')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('PersonaController@index')}}">Persona</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Persona {{$persona->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($persona,['method'=>'PATCH','route'=>['persona.update',$persona->Id_persona]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre..." value="{{$persona->nombre}}">
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos..." value="{{$persona->apellidos}}">
        </div>
        <div class="form-group">
                <label for="cargo">Cargo</label>
                <input type="text" name="cargo" class="form-control" placeholder="Cargo..." value="{{$persona->cargo}}">
            </div>
        <div class="alert alert-info" role="alert">
        <strong>Sector Social</strong>: Se refiere a todas aquellas organizaciones no gubernamentales u organizaciones sociales 
        <br>
        <strong>Sector Institucional/Gubernamental</strong>:Son todas aquellas dependencias gubernamentales, así como empresas e instituciones 
        </div>
       <div class="form-group">
            <label for="sector">Elige un sector:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="sector">Sector</label>
            </div>

                <select class="custom-select" name="extra" id="extra">
                    @if($persona->extra==2)
                    <option value="Sin Sector">Sin Sector</option>
                    <option value="Sector Social">Sector Social</option>
                    <option value="Sector Institucional/Gubernamental" selected>Sector Institucional/Gubernamental</option>
                    @elseif($persona->extra==1)
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

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('persona.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection