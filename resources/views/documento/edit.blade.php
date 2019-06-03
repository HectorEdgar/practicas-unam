@extends ('layouts.admin') @section ('contenido')
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
				<li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
				<li class="breadcrumb-item active" aria-current="page">Crear Documento</li>
			</ol>
		</nav>
		<div class="clearfix"><br></div><div class="clearfix"></div>
		
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Editar Documento: {{ $documento->titulo}}</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		
		@endif 

		@if (session('status'))
		<div class="alert alert-danger">
			{{ session('status') }}
		</div>
		@endif 
		
		
		{!!Form::model($documento,['method'=>'PATCH','route'=>['documento.update',$documento->Id_doc]])!!} {{Form::token()}}

		<div class="row">


			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="titulo">Título documento</label>
					<input type="text" name="titulo" class="form-control" placeholder="Título" value="{{$documento->titulo}}" />
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="lugar_public_pais">País publicación</label>
					<input type="text" name="lugar_public_pais" class="form-control" placeholder="País de publicación" value="{{$documento->lugar_public_pais}}"
					/>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="lugar_public_edo">Estado publicación </label>
					<input type="text" name="lugar_public_edo" class="form-control" placeholder="Estado de publicación " value="{{$documento->lugar_public_edo}}"
					/>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="derecho_autor">Derechos autor</label>

					<select name="derecho_autor" id="derecho_autor" class="form-control">



						<option value="1" selected>Sí</option>
						<option value="0">No</option>



					</select>
				</div>

			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="fechas">
				<div class="card w-90">
					<h6 class="card-header">Fecha de publicación</h6>
					<div class="card-body">
						<div class="form-group">
							<div class="form-check">

								
								<input class="form-check-input fechaNormal" type="radio" name="fecha_publi" value="1"  {{$documento->fecha_publi==1 ? 'checked':''}} >

								


								<label class="form-check-label" for="fecha_publi">
									Fecha de publicación Normal
								</label>
								<br/>
								<p id="inFechaNormal">
									<input type="text" placeholder="Fecha de publicación" name="fechaNormalValor" id="fechaNormalValor" value={{$fecha or ' '}} >

								</p>



							</div>
							<br/>

							<div class="form-check">

							
								<input class="form-check-input fechaNormal" type="radio" name="fecha_publi" value="2" {{$documento->fecha_publi==2 ? 'checked':''}}>
							
								<label class="form-check-label" for="fecha_publi">
									Fecha de publicación por período
								</label>


								<div class="form-row" id="inFechaExtra">

									<div class="form-group col-md-3">


										<label for="fechaExtraMes">Del mes</label>
										<select id="fechaExtraMes" class="form-control" name="fechaExtraMes">
											@foreach ($mesesFecha as $value)

											<option value="{{ $value }}">{{$value}}</option>

											@endforeach


										</select>
									</div>

									<div class="form-row">
										<div class="form-group col-md-4">
											<label for="fechaExtraAlMes">Al mes </label>
											<select id="fechaExtraAlMes" class="form-control" name="fechaExtraAlMes">
												@foreach ($mesesFecha as $value)

												<option value="{{ $value }}">{{$value}}</option>

												@endforeach

											</select>
										</div>

										<div class="form-group col-md-3">
											<label for="fechaExtraAño">Año</label>
											@if(is_null($fechaExtra))
											<input type="number" class="form-control" id="fechaExtraAño" min="1600" name="fechaExtraAño" /> @else
											<input type="number" class="form-control" id="fechaExtraAño" min="1600" name="fechaExtraAño" value="{{$fechaExtra ->anio}}"
											/> @endif
										</div>

									</div>





								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="url">Url</label>
					<input type="text" name="url" class="form-control" placeholder="url" value="{{$documento->url}}" />
				</div>
			</div>




			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="fecha_consulta">Fecha de consulta</label>
					<input type="date" name="fecha_consulta" class="form-control" placeholder="Fecha consulta" value="{{$documento->fecha_consulta}}"
					/>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="poblacion">Población</label>

					<select name="poblacion" id="poblacion" class="form-control">


						<option value="0">Ninguno</option>
						<option value="1">Afrodescendiente</option>
						<option value="2">Indígena</option>
						<option value="3">Ambos</option>



					</select>

				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="tipo">Tipo</label>

					<select name="tipo" id="Tipo" class="form-control" >
						@foreach ($categorias as $cat)

						<option value="{{ $cat->id_cata_doc }}">{{$cat->tipo_doc }}</option>


						@endforeach
					</select>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="notas">Notas</label>

					<div class="form-group">
						<textarea rows="3" cols="55" name="notas" class="form-group" value="{{$documento->fecha_registro}}"></textarea>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="boletin" hidden> 

				<div class="row">

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						

						<div class="form-group ">
							<label for="boletin">No. Boletín ó Revista:</label>

						<input type="text" name="boletinRevista-num" placeholder="Núm. 1" class="form-control" value="{{$tipoDocumento->num_revista or ''  }}" >

							<label for="boletin">Volumen:</label>

							<input type="text" name="boletinRevista-vol" placeholder="Vol. 1" class="form-control" value="{{$tipoDocumento->volumen or '' }}">

						</div>

					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						

							<div class="form-group ">


							<label for="boletin">Páginas del Boletín ó Revista:</label>

							<input type="text" name="boletinRevista-pag" placeholder="150 Págs" class="form-control" value="{{$tipoDocumento->pag or ''  }}">


							<label for="boletin">Año:</label>

							<input type="text" name="boletinRevista-anio" placeholder="Año 1" class="form-control" value="{{$tipoDocumento->anio or '' }}">
						</div>

					</div>

				</div>
			</div>




			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="libro" hidden> 

					<div class="row">
	
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							
	
							<div class="form-group ">
								
								<label for="libro">No. Edición:</label>
	
								<input type="text" name="libro-ed" placeholder="1a Ed." class="form-control"  value="{{$tipoDocumento->edicion or '' }}" >
	
								<label for="libro">Responsable del Prólogo:</label>
	
								<input type="text" name="libro-resPrologo" placeholder="Res. del Prólogo" class="form-control" value="{{$tipoDocumento->prologo or '' }}">


								<label for="libro">Colección:</label>
	
								<input type="text" name="libro-col" placeholder="Colección" class="form-control" value="{{$tipoDocumento->coleccion or '' }}">


								<label for="libro">Serie:</label>
	
								<input type="text" name="libro-serie" placeholder="Serie" class="form-control" value="{{$tipoDocumento->serie or '' }}">


								<label for="libro">Páginas del Libro:</label>
	
								<input type="text" name="libro-pag" placeholder="500 Págs" class="form-control" value="{{$tipoDocumento->paginalib or '' }}">

								
								
	
							</div>
	
						</div>
	
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							
	
								<div class="form-group ">
	
	
								<label for="libro"> Nombre del Traductor:</label>
	
								<input type="text" name="libro-traductor" placeholder="Nom. Traductor" class="form-control" value="{{$tipoDocumento->traductor or '' }}" >
	
	
								<label for="libro"> Responsable de la Introducción:</label>
	
								<input type="text" name="libro-introduccion" placeholder="Resp. Introducción" class="form-control" value="{{$tipoDocumento->introduccion or '' }}" >


								<label for="libro"> Tomos:</label>
	
								<input type="text" name="libro-tomos" placeholder="tomos" class="form-control" value="{{$tipoDocumento->tomos or '' }}">

								<label for="libro">No. de Serie:</label>
	
								<input type="number" name="libro-noserie" min="0" class="form-control" value="{{$tipoDocumento->noserie or '' }}">

								<label for="libro">No. de Colección:</label>
	
								<input type="number" name="libro-nocol" min="0" class="form-control" value="{{$tipoDocumento->nocol or '' }}">

								<label for="libro">Volumenes:</label>
	
								<input type="text" name="libro-vol"  class="form-control" placeholder="Volmuen" value="{{$tipoDocumento->volumen  or '' }}">


							</div>
	
						</div>
	
					</div>
				</div>

				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="ponencia" hidden> 
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								
		
								<div class="form-group ">
									<label for="ponencia">Nombre del Evento:</label>
		
									<input type="text" name="ponencia-nombre" placeholder="Nombre del evento" class="form-control" value="{{$tipoDocumento->evento or '' }}">
		
									<label for="ponencia">Fecha del Evento:</label>
									<input type="text" name="ponencia-fecha" class="form-control" placeholder="1 / 1-15 may. 2010" value="{{$tipoDocumento->fecha_pesentacion or '' }}">
								</div>
		
							</div>
		
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		
									<div class="form-group ">
		
									<label for="ponencia"> Lugar del Evento::</label>
		
									<input type="text" name="ponecia-lugar" placeholder="Lugar" class="form-control" value="{{$tipoDocumento->lugar_presentacion or '' }}">

									<label for="ponencia">Paginas:</label>
		

									<input type="text" name="ponencia-pag" placeholder="10 Págs" class="form-control"  value="{{$tipoDocumento->paginas or '' }}">
	 
								</div>
		
							</div>
		
						</div>
					</div>


					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="tesis" hidden> 
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
									<div class="form-group ">
										<label for="tesis">Asesor:</label>
			
										<input type="text" name="tesis-asesor" placeholder="Asesor" class="form-control" value="{{$tipoDocumento->asesor or '' }}">
			
										<label for="tesis">Grado y Especialidad::</label>
										<input type="text" name="tesis-grado" placeholder="Ejem. Tésis de Licenciatura en Sociología" class="form-control" value="{{$tipoDocumento->grado or '' }}">
									</div>
			
								</div>
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			
										<div class="form-group ">
			
										
	
										<label for="tesis">No. Páginas:</label>
			
										<input type="text" name="tesis-pag" placeholder="100 Págs" class="form-control" value="{{$tipoDocumento->num_paginas or '' }}">
		
									</div>
			
								</div>
			
							</div>
						</div>


						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="artRevista" hidden> 
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
									<div class="form-group ">
										<label for="artRevista"> Nombre del Boletín ó Revista:</label>
			
										<input type="text" name="artRevista-nombre" placeholder="Nombre del Boletín ó Revista" class="form-control" value="{{$tipoDocumento->nombre_revista or '' }}" >
			
										<label for="artRevista">No. Boletín ó Revista:</label>
			
										<input type="text" name="artRevista-num" placeholder="Núm. 1" class="form-control" value="{{$tipoDocumento->num_revista or '' }}">

										<label for="artRevista">Páginas del Artículo:</label>
			
										<input type="text" name="artRevista-pag"  placeholder="Págs. 1-15" class="form-control" value="{{$tipoDocumento->pag or '' }}">
									</div>
			
								</div>
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			
										<div class="form-group ">
			
										
	
											<label for="artRevista">Volumen:</label>
			
											<input type="text" name="artRevista-vol" placeholder="Vol. 1" class="form-control" value="{{$tipoDocumento->volumen or '' }}">

											<label for="artRevista">Año:</label>
			
											<input type="text" name="artRevista-anio" class="form-control"  placeholder="Año 1" value="{{$tipoDocumento->anio or '' }}">
		
									</div>
			
								</div>
			
							</div>
						</div>



						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="capLibro" hidden> 
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
									<div class="form-group ">
										<label for="capLibro">Nombre del Libro:</label>
			
										<input type="text" name="capLibro-nombre" placeholder="Nombre del libro" class="form-control" value="{{$tipoDocumento->nombre_libro or '' }}">
			
										<label for="capLibro">No. Edición:</label>
			
										<input type="text" name="capLibro-ed"  class="form-control"  placeholder="1a Ed"  value="{{$tipoDocumento->edicion or '' }}">

										<label for="capLibro">Volumen:</label>
			
										<input type="text" name="capLibro-volumen"class="form-control"  placeholder="Volumen"  value="{{$tipoDocumento->volumen or '' }}">

										<label for="capLibro">No. de Colección:</label>
			
										<input type="text" name="capLibro-Numcole"class="form-control"  placeholder="No. de Colección"  value="{{$tipoDocumento->nocol or '' }}">

										<label for="capLibro">No. de Serie:</label>
			
										<input type="text" name="capLibro-serie"class="form-control"  placeholder="No. de Serie"  value="{{$tipoDocumento->noserie or '' }}">

										<label for="capLibro">Páginas del Capítulo:</label>
			
										<input type="text" name="capLibro-pag"class="form-control"  placeholder="Páginas del Capítulo"  value="{{$tipoDocumento->paginas or '' }}">
									</div>
			
								</div>
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			
										<div class="form-group ">
			
										
	
											<label for="capLibro">Autor General:</label>
			
											<input type="text" name="capLibro-autor"class="form-control"  placeholder="Autor General"  value="{{$tipoDocumento->autorgral or '' }}">

											<label for="capLibro">Tomo:</label>
			
											<input type="text" name="capLibro-tomo"class="form-control"  placeholder="Tomo"  value="{{$tipoDocumento->tomos or '' }}">

											<label for="capLibro">Colección:</label>
			
											<input type="text" name="capLibro-col"class="form-control"  placeholder="Colección"  value="{{$tipoDocumento->coleccion or '' }}">

											<label for="capLibro">Serie:</label>
			
											<input type="text" name="capLibro-serie"class="form-control"  placeholder="Serie"  value="{{$tipoDocumento->serie or '' }}">

											<label for="capLibro">Nombre del Traductor:</label>
			
											<input type="text" name="capLibro-traductor"class="form-control"  placeholder="Nombre del Traductor"  value="{{$tipoDocumento->traductor or '' }}">
		
									</div>
			
								</div>
			
							</div>
						</div>


						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="revista" hidden> 

							<div class="row">
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
									<div class="form-group ">
										<label for="revista">No. Boletín ó Revista:</label>
			
										<input type="text" name="revista-num" placeholder="Núm. 1" class="form-control"  value="{{$tipoDocumento->num_revista or '' }}">
			
										<label for="revista">Volumen:</label>
			
										<input type="text" name="revista-vol" placeholder="Vol. 1" class="form-control"  value="{{$tipoDocumento->volumen or '' }}">
			
									</div>
			
								</div>
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
										<div class="form-group ">
			
			
										<label for="revista">Páginas del Boletín ó Revista:</label>
			
										<input type="text" name="revista-pag" placeholder="150 Págs" class="form-control"  value="{{$tipoDocumento->pag or '' }}">
			
			
										<label for="revista">Año:</label>
			
										<input type="text" name="revista-anio" placeholder="Año 1" class="form-control"  value="{{$tipoDocumento->anio or '' }}">
									</div>
			
								</div>
			
							</div>
						</div>


						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 tipoDocumento" id="artBoletin" hidden> 
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									
			
									<div class="form-group ">
										<label for="artBoletin"> Nombre del Boletín ó Revista:</label>
			
										<input type="text" name="artBoletin-nombre" placeholder="Nombre del Boletín ó Revista" class="form-control"  value="{{$tipoDocumento->nombre_revista or '' }}">
			
										<label for="artBoletin">No. Boletín ó Revista:</label>
			
										<input type="text" name="artBoletin-num" placeholder="Núm. 1" class="form-control"  value="{{$tipoDocumento->num_revista or '' }}">

										<label for="artBoletin">Páginas del Artículo:</label>
			
										<input type="text" name="artBoletin-pag" placeholder="Págs. 1-15" class="form-control"  value="{{$tipoDocumento->pag or '' }}">
									</div>
			
								</div>
			
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			
										<div class="form-group ">
			
										
	
											<label for="artBoletin">Volumen:</label>
			
											<input type="text" name="artBoletin-vol" placeholder="Vol. 1" class="form-control"  value="{{$tipoDocumento->volumen or '' }}">

											<label for="artBoletin">Año:</label>
			
											<input type="text" name="artBoletin-anio" class="form-control"  placeholder="Año 1"  value="{{$tipoDocumento->anio or '' }}">
		
									</div>
			
								</div>
			
							</div>
						</div>






					</div>
					<div class="d-flex align-items-center justify-content-center h-100 " >
				<div class="form-group">
					<button class="btn btn-primary" type="submit">Guardar</button>
					<a class="btn btn-danger" href="{{ route('documento.index') }}">Cancelar</a>
				</div>
			</div>

			{!!Form::close()!!}

		
	</div>



	<script  type="text/javascript">
	   

    if ('<?php echo $documento->fecha_publi;?>' == 2) {
		$("#inFechaNormal").hide();
		$("#inFechaExtra").show();

			
		$("#fechaExtraMes").val('<?php echo ucfirst($fechaExtra->mes);?>');
		$("#fechaExtraAlMes").val('<?php echo ucfirst($fechaExtra->mes2);?>');



    } else {

		$("#inFechaExtra").hide();
		$("#inFechaNormal").show();

	}
	


    $("#derecho_autor").val('<?php echo $documento->derecho_autor;?>');
    $("#poblacion").val('<?php echo $documento->poblacion;?>');
    $("#Tipo").val('<?php echo $documento->tipo;?>');
	</script>
		<script type="text/javascript" src="{{asset('js/documento/edit.js')}}"/>

	@endsection