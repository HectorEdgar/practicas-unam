@extends ('layouts.admin')
@section('titulo')
    Index de Documento
@endsection
@section ('contenido')
@if(Session::has('message'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message')}}
</div>
@endif
@if(Session::has('message2'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message2')}}
</div>
@endif
@if(Session::has('message3'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message3')}}
</div>
@endif
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<!--Etiquetas de breadcrum-->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
				<li class="breadcrumb-item active" aria-current="page">Documentos</li>
			</ol>
		</nav>
		<h3 class="text-center">
			Listado de documentos   
		</h3>
		<div class="clearfix"><br></div><div class="clearfix"></div>
		
	</div>
</div>



 <!--Etiquetas del formulario de busqueda-->
 <div class="row">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		@include('documento.search')
	</div>
	<!--Etiquetas de la imagen de agregar-->
	<div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
		<a href="{{url('/documento/create')}}" class="btn btn-outline-success col-sm-12 col-xs-12" >
			<img width="30px" src="{{asset('imgs/agregar.svg')}}"></img>
		</a>
	</div>
   
</div>
<br/>

<div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
	<table class="table table-hover table-sm ">
				<thead class="thead-dark">
					<tr>
						<th scope="col" class="text-center align-middle">Id</th>
						<th scope="col" class="text-center align-middle">Título</th>
						<th scope="col" class="text-center align-middle">Fecha Consulta</th>
						<th scope="col" class="text-center align-middle">Fecha Registro</th>
						<th scope="col" class="text-center align-middle">Opciones</th>
					</tr>

				</thead>
               @foreach ($documento as $doc)
				<tr>
					<th scope="row" class="text-center align-middle">{{ $doc->Id_doc}}</th>
					<td class="text-center align-middle">{{ $doc->titulo}}</td>
					<td class="text-center align-middle">{{ $doc->fecha_consulta}}</td>
					<td class="text-center align-middle">{{ $doc->fecha_registro}}</td>
					<td class="text-center align-middle">
							<a href="{{URL::action('DocumentoController@edit',$doc->Id_doc)}}">
							<img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
							</a>
							<a href="" data-target="#modal-delete-{{$doc->Id_doc}}" data-toggle="modal">
							<img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
							</a>
							<a href="{{URL::action('DocumentoController@show',$doc->Id_doc)}}">
							<img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
							</a>
					</td>
				</tr>
				@include('documento.modal')
				@endforeach
			</table>
		
			<!--RENDER ES EL PAGINADOR -->
			

			@include('documento.paginador')
		</div>




@endsection