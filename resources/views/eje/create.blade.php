@extends('layouts.admin') 
@section('titulo') Crear Eje
@endsection
 
@section('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('EjeController@index')}}">Eje</a></li>
				<li class="breadcrumb-item active" aria-current="page">Agregar</li>
			</ol>
		</nav>
		<h3>Nuevo Eje</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif {!!Form::open(array('url'=>'/eje','method'=>'POST','autocomplete'=>'off')) !!} {{Form::token()}}
		<div class="form-group">
			<label for="nombre">Nombre</label>
			<input type="text" name="nombre" class="form-control" placeholder="Nombre...">
		</div>
		<div class="form-group">
			<label for="descripcion">Descripción</label>
			<input type="text" name="descripcion" class="form-control" placeholder="Descripción...">
		</div>
		<div class="form-group">
			<label for="area">Área</label>
			<input type="text" name="area" class="form-control" placeholder="Área...">
		</div>
		<div class="form-group">
			<label for="poblacion">Población</label>
			<input type="text" name="poblacion" class="form-control" placeholder="Población...">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<a class="btn btn-secondary" href="{{ route('eje.index') }}">Cancelar</a>
		</div>
		{!!Form::close()!!}
	</div>
</div>
@endsection

