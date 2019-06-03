@extends('layouts.admin') 
@section('titulo') Index de eje
@endsection
 
@section('contenido')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<!--Etiquetas de breadcrum-->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item"><a href="{{URL::action('EjeController@index')}}">Eje</a></li>
				<li class="breadcrumb-item active" aria-current="page">Index</li>
			</ol>
		</nav>
		<h3 class="text-center">
			Listado de Ejes
		</h3>
		<div class="clearfix"><br></div>
		<div class="clearfix"></div>

	</div>
</div>

<!--Etiquetas del formulario de busqueda-->
<div class="row">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
	@include('eje.search')
	</div>
	<!--Etiquetas de la imagen de agregar-->
	<div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
		<a href="{{url('/eje')}}/create" class="btn btn-outline-success col-sm-12 col-xs-12">
                    <img width="30px" src="{{asset('imgs/agregar.svg')}}"></img>
                </a>
	</div>

</div>

<br>
<div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">

	<table class="table table-hover table-sm ">

		<thead class="thead-dark">
			<tr>
				<th scope="col" class="text-center align-middle">Id</th>
				<th scope="col" class="text-center align-middle">Nombre</th>
				<th scope="col" class="text-center align-middle">Descipción</th>
				<th scope="col" class="text-center align-middle">Área</th>
				<th scope="col" class="text-center align-middle">Población</th>
				<th scope="col" class="text-center align-middle ">Opciones</th>
			</tr>
		</thead>

		<tbody>
			@foreach($ejes as $item)
			<tr>
				<th scope="row" class="text-center align-middle">{{$item ->Id_eje}}</td>
					<td class="text-center align-middle">{{$item ->nombre}}</td>
					<td class="text-center align-middle">{{$item ->descripcion}}</td>
					<td class="text-center align-middle">{{$item ->area}}</td>
					<td class="text-center align-middle">{{$item ->poblacion}}</td>
					<td class="text-center align-middle ">
						<a href="{{URL::action('EjeController@edit',$item ->Id_eje)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
						<a href="" data-target="#modal-delete-{{$item->Id_eje}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
					</td>
			</tr>
	@include('eje.modal') @endforeach
		</tbody>
	</table>
	{{--$ejees->links()--}}
	@include('eje.paginador')
</div>
@endsection
