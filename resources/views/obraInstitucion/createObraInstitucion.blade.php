<!--Esta es la vista que crea un nuevo institucion
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Institución
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_institucion')}}/ligar/{{$obra->id_obra}}">Administrar Obra e Institución</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Institución</li>
                </ol>
        </nav>
        <h3>Nuevo Institución</h3>
        <h5>Esta nueva institución se ligará automaticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obrainstitucion-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_institucion.update',$obra->id_obra]]) !!}
        {{Form::token()}}
      <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="nombre">Nombre de Institución</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Institución..">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="siglas">Siglas</label>
                    <input type="text" name="siglas" class="form-control" placeholder="Siglas..">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
            <label for="pais">Elige un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="pais">País</label>
            </div>

                <select class="custom-select" name="pais" id="pais">
                    <option value="0" selected>-- Seleccione --</option>
                    @foreach($paises as $item)
                <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="localidad">Localidad</label>
                    <input type="text" name="localidad" class="form-control" placeholder="localidad..">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-info" role="alert">
                <strong>Sector Social</strong>: Se refiere a todas aquellas organizaciones no gubernamentales u organizaciones sociales 
                <br>
                <strong>Sector Institucional/Gubernamental</strong>:Son todas aquellas dependencias gubernamentales, así como empresas e instituciones 
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
                        <option value="Sin Sector" selected>Sin Sector</option>
                        <option value="Sector Social">Sector Social</option>
                        <option value="Sector Institucional/Gubernamental">Sector Institucional/Gubernamental</option>
                    </select>
                </div>
        </div>
        <div class="form-group">
                        <label for="sel1">Selecciona el tipo de Relación:</label>
                        <select class="form-control"  id="relacion" name="relacion">
                            <option value="1" selected>Directa</option>
                            <option value="2">Indirecta</option>
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