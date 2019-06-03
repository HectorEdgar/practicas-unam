@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Ponencia: {{ $ponencia->evento}}</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
				@endforeach
				</ul>
			</div>
			@endif

			{!!Form::model($ponencia,['method'=>'PATCH','route'=>['ponencia.update',$ponencia->fk_doc]])!!}
            {{Form::token()}}
            <div class="form-group">
            	<label for="fk_doc">ID de la Ponencia</label>
            	<input type="text" name="fk_doc" class="form-control" placeholder="Id" value="{{$ponencia->fk_doc}}" readonly>
            </div>

            <div class="form-group">
            	<label for="evento">Nombre del Evento</label>
            	<input type="text" name="evento" class="form-control" placeholder="Evento" value="{{$ponencia->evento}}">
            </div>

			<div class="form-group">
            	<label for="lugar_presentacion">Lugar de Presentación</label>
            	<input type="text" name="lugar_presentacion" class="form-control" placeholder="Lugar Presentación" value="{{$ponencia->lugar_presentacion}}">
            </div>

			

			<div class="form-group">
            	<label for="fecha_pesentacion">Fecha  de Presentación</label>
            	<input type="text" name="fecha_pesentacion" class="form-control" placeholder="Fecha Presentación" value="{{$ponencia->fecha_pesentacion}}">
            </div>

			<div class="form-group">
            	<label for="paginas">Páginas</label>
            	<input type="text" name="paginas" class="form-control" placeholder="Páginas" value="{{$ponencia->paginas}}">
            </div>



            <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
            	<button class="btn btn-danger" type="reset">Cancelar</button>
            </div>

			{!!Form::close()!!}		
            
		</div>
	</div>
@endsection