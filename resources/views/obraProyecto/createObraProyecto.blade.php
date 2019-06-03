<!--Esta es la vista que crea un nuevo proyecto
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Proyecto
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_proyecto')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Proyecto</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Proyecto</li>
                </ol>
        </nav>
        <h3>Nuevo Proyecto</h3>
        <h5>Este nuevo Proyecto se ligará automáticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obraproyecto-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_proyecto.update',$obra->id_obra]]) !!}
        {{Form::token()}}
       <div class="form-group">
            	<label for="proyecto">Nombre</label>
            	<input type="text" name="proyecto" class="form-control" placeholder="Nombre...">
            </div>
            
       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
            	<button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection