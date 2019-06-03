@extends('layouts.admin') 
@section('titulo') Crear Editor
@endsection
 
@section('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('EditorController@index')}}">Editor</a></li>
				<li class="breadcrumb-item active" aria-current="page">Agregar</li>
			</ol>
		</nav>
		<h3>Nuevo Editor</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif 
		{!!Form::open(array('url'=>'/editor','method'=>'POST','autocomplete'=>'off')) !!} {{Form::token()}}
		<div class="form-group">
			<label for="editor">Nombre del Editor</label>
			<input type="text" name="editor" class="form-control" placeholder="Nombre">
		</div>


		@if(Auth::user()->hasAnyRole('revisor')) 

		<div class="form-group">
			<label for="editor">País</label>
			<input type="text" name="pais" class="form-control" placeholder="País">
		</div>

		<div class="form-group">
			<label for="editor">Entidad Federativa </label>
			<input type="text" name="estado" class="form-control" placeholder="Entidad Federativa">
		</div>

		<div class="form-group">
			<label for="der_autor">Derechos DE autor</label>

			<select name="der_autor" class="form-control">
				<option value="1">Sí</option>
				<option value="0" selected>No</option>
			</select>
		</div>
		
		
		
		@endif 
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<a class="btn btn-secondary" href="{{ route('editor.index') }}">Cancelar</a>
		</div>
		{!!Form::close()!!}
	</div>
</div>
@endsection

