@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>Listado de 	Ponencias  <a href="ponencia/create"><button class="btn btn-success">Nuevo</button></a></h3>

		<!--ESTO ES COMO UNA MASTER PAGE-->
		@include('ponencia.search')
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<th>Id</th>
					<th>Evento</th>
					<th>Lugar presentación</th>
					<th>Fecha presentación</th>
					<th>Páginas</th>
					<td>Opciones</td>
				</thead>
               @foreach ($ponencias as $pon)
				<tr>
					<td>{{ $pon->fk_doc}}</td>
					<td>{{ $pon->evento}}</td>
					<td>{{ $pon->lugar_presentacion}}</td>
					<td>{{ $pon->fecha_pesentacion}}</td>
					<td>{{ $pon->paginas}}</td>

					<td>
						<a href="{{URL::action('PonenciaController@edit',$pon->fk_doc)}}"><button class="btn btn-info">Editar</button></a>
                         <a href="" data-target="#modal-delete-{{$pon->fk_doc}}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
					</td>
				</tr>
				@include('ponencia.modal')
				@endforeach
			</table>
		</div>
		<!--RENDER ES EL PAGINADOR -->
		{{$ponencias->render()}}
	</div>
</div>
@endsection