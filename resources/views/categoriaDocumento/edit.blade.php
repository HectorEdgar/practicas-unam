@extends('layouts.admin') 
@section('titulo') Editar Categoría de Documento
@endsection
 
@section('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('CategoriaDocumentoController@index')}}">Categoría Documento</a></li>
				<li class="breadcrumb-item active" aria-current="page">Editar</li>
			</ol>
		</nav>
		<h3>Editar Categoría del Documento: {{$categoriasDocumento->tipo_doc}}</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif {!!Form::model($categoriasDocumento,['method'=>'PATCH','route'=>['categoriaDocumento.update',$categoriasDocumento->id_cata_doc]])!!}
		{{Form::token()}}
		
		<div class="form-group">
			<label for="tipo_doc">Nombre de la categoría</label>
			<input type="text" name="tipo_doc" class="form-control" placeholder="nombre" value="{{$categoriasDocumento->tipo_doc}}">
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<a class="btn btn-secondary" href="{{ route('categoriaDocumento.index') }}">Cancelar</a>
		</div>
		
		{!!Form::close()!!}
	</div>
</div>
@endsection


