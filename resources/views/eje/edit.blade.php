@extends('layouts.admin') 
@section('titulo') Editar Eje
@endsection
 
@section('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('EjeController@index')}}">Eje</a></li>
				<li class="breadcrumb-item active" aria-current="page">Editar</li>
			</ol>
		</nav>
		<h3>Editar Eje {{$eje->nombre}}</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif {!!Form::model($eje,['method'=>'PATCH','route'=>['eje.update',$eje->Id_eje]]) !!} {{Form::token()}}
		<div class="form-group">
			<label for="nombre">Nombre</label>
			<input type="text" name="nombre" class="form-control" placeholder="Nombre..." value="{{$eje->nombre}}">
		</div>
		<div class="form-group">
			<label for="descripcion">Descripción</label>
			<input type="text" name="descripcion" class="form-control" placeholder="Descripción..." value="{{$eje->descripcion}}">
		</div>
		<div class="form-group">
			<label for="area">Área</label>
			<input type="text" name="area" class="form-control" placeholder="Área..." value="{{$eje->area}}">
		</div>
		<div class="form-group">
			<label for="poblacion">Población</label>
			<input type="text" name="poblacion" class="form-control" placeholder="Población..." value="{{$eje->poblacion}}">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<a class="btn btn-secondary" href="{{ route('eje.index') }}">Cancelar</a>
		</div>
		{!!Form::close()!!}
	</div>
</div>
@endsection
