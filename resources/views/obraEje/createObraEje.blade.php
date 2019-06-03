<!--Esta es la vista que crea un nuevo eje
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Eje
@endsection
@section('contenido')
@if(Session::has('flash_message'))
<div class="alert alert-success">
  <strong>Exito!</strong> {{Session::get('flash_message')}}
</div>
@endif

@if(Session::has('flash_message2'))
<div class="alert alert-warning">
  <strong>Aviso!</strong> {{Session::get('flash_message2')}}
</div>
@endif

@if(Session::has('flash_message3'))
<div class="alert alert-success">
  <strong>Exito!</strong> {{Session::get('flash_message3')}}
</div>
@endif

@if(Session::has('flash_message4'))
<div class="alert alert-success">
  <strong>Exito!</strong>{{Session::get('flash_message4')}}
</div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_eje')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Eje</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Eje</li>
                </ol>
        </nav>
        <h3>Nuevo Eje</h3>
        <h5>Este nuevo eje se ligará automaticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obraeje-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_eje.update',$obra->id_obra]]) !!}
        {{Form::token()}}
       <div class="form-group">
            	<label for="nombre">Nombre</label>
            	<input type="text" name="nombre" class="form-control" placeholder="Nombre..." required>
            </div>
            <div class="form-group">
            	<label for="descripcion">Descripción</label>
            	<input type="text" name="descripcion" class="form-control" placeholder="Descripción..." required>
			</div>
			<div class="form-group">
				<label for="area">Area</label>
				<input type="text" name="area" class="form-control" placeholder="Area..." required>
			</div>
			<div class="form-group">
				<label for="poblacion">Población</label>
				<input type="text" name="poblacion" class="form-control" placeholder="Población..." required>
			</div>
       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
            	<button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection