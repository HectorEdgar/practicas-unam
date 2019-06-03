@extends('layouts.admin') 
@section('titulo') Index de editor
@endsection
 
@section('contenido')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<!--Etiquetas de breadcrum-->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('EditorController@index')}}">Editor</a></li>
				<li class="breadcrumb-item active" aria-current="page">Index</li>
			</ol>
		</nav>
		<h3 class="text-center">
			Listado de Editores
		</h3>
		<div class="clearfix"><br></div>
		<div class="clearfix"></div>

	</div>
</div>

<!--Etiquetas del formulario de busqueda-->
<div class="row">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
	@include('editor.search')
	</div>


</div>

<br>
<div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">

	<table class="table table-hover table-sm ">

		<thead class="thead-dark">
			<tr>
				<th scope="col" class="text-center align-middle">Id</th>
				<th scope="col" class="text-center align-middle">Editor</th>
				<th scope="col" class="text-center align-middle">País</th>
				<th scope="col" class="text-center align-middle">Entidad Federativa</th>
				<th scope="col" class="text-center align-middle">Derechos</th>
				<th scope="col" class="text-center align-middle ">Opciones</th>
			</tr>
		</thead>

		<tbody>
			@foreach($editores as $item)
			<tr>
				<th scope="row" class="text-center align-middle">{{$item ->id_editor}}</th>
					<td class="text-center align-middle">{{$item ->editor}}</td>
					<td class="text-center align-middle">{{$item ->pais}}</td>
					<td class="text-center align-middle">{{$item ->estado}}</td>
					<td class="text-center align-middle">{{$item ->der_autor==1?'Si':'No'}}</td>

					<td class="text-center align-middle ">
						<a href="{{URL::action('EditorController@edit',$item ->id_editor)}}">
                            <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                        </a>
						<a href="" data-target="#modal-delete-{{$item->id_editor}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
						</a>
						<a href="{{URL::action('EditorController@show',$item ->id_editor)}}">
							<img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
					 </a>
					</td>
					
			</tr>
		@include('editor.modal') 
	@endforeach
		</tbody>
	</table>
	{{--$editores->links()--}}
	@include('editor.paginador')
</div>
@endsection

