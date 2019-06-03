<!--Esta es la vista que crea un nuevo obra
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Obra
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_obra')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Obra</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Obra</li>
                </ol>
        </nav>
        <h3>Nueva Obra</h3>
        <h5>Este nueva obra se ligará automaticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obraobra-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_obra.update',$obra->id_obra]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="nombre">Nombre de la Infraestructura:</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre..">
        </div>

         <div class="form-group">
            <div class="alert alert-info" role="alert">
                <strong>Especifíque:</strong> Esta Infraestructura se trata de:
                <select class="custom-select" name="tipo" id="tipo">
                    <option value="obra">Obra</option>
                    <option value="complejo">Complejo</option>
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