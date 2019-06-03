<!--Esta es la vista que crea un nuevo persona
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Persona
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_persona')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Persona</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Persona</li>
                </ol>
        </nav>
        <h3>Nueva Persona</h3>
        <h5>Este nueva Persona se ligará automaticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obrapersona-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_persona.update',$obra->id_obra]]) !!}
        {{Form::token()}}
       
            <div class="form-group">
            <label for="cargo">Cargo</label>
            <input type="text" name="cargo" class="form-control" placeholder="Cargo..">
        </div>
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre..">
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos..">
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
                    <option value="Sin Sector" selected>Sin Sector</option>
                    <option value="Sector Social">Sector Social</option>
                    <option value="Sector Institucional/Gubernamental">Sector Institucional/Gubernamental</option>
                </select>
            </div>
        </div>
       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
            	<button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection