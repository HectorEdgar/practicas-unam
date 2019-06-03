<!--Esta es la vista que crea un nuevo lugar
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Crear Lugar
@endsection
@section('contenido')

@if(Session::has('messageError'))
<div class="alert alert-warning">
  <strong>¡Aviso! </strong> {{Session::get('messageError')}}
</div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_lugar')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Lugar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Lugar</li>
                </ol>
        </nav>
        <h3>Nuevo Lugar</h3>
        <h5>Este nuevo lugar se ligará automáticamente con la obra ({{$obra->id_obra}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obralugar-->
      {!!Form::model($obra,['method'=>'PATCH','route'=>['obra_lugar.update',$obra->id_obra]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="ubicacion">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" placeholder="Ubicación...">
        </div>
          <div class="form-group">
            <label for="pais">Elige un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="pais">País</label>
            </div>

                <select class="custom-select" name="pais" id="pais">
                    <option value="0" selected>-- Seleccione --</option>
                    @foreach($paises as $item)
                <option value="{{$item->id_pais}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>

          <div class="form-group">
            <label for="sector">Eliga una Región Geográfica:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="region">Región</label>
            </div>

                <select class="custom-select" name="region_geografica" id="region_geografica">
                    <option value="0" selected>-- Seleccione --</option>
                    @foreach($regiones as $item)
                    <option value="{{$item->id_region}}">{{$item->nombrereg}}</option>
                    @endforeach
                </select>
            </div>
        </div>

<div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="Latitud">Latitud:</label>
                    <input class="form-control" type="text" style="width:400px" id="latitud" name="latitud" />
                </div>
                <div class="form-group">
                    <label for="Longitud">Longitud:</label>
                    <input class="form-control" type="text" style="width:400px" id="longitud" name="longitud" />
                </div>

            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="complejo">Complejo</label>
                    <select class="form-control selectpicker" data-live-search="true" name="complejo">
                        <option data-tokens="- - - - " value="0" selected>- - Sin Complejo - -</option>
                         @foreach($complejos as $item)
                        <option data-tokens="{{$item->nombre}}" value="{{$item->id_obra}}">{{$item->nombre}}</option>
                         @endforeach
                    </select>

                </div>
                </div>
        </div>

        <div class="form-group">
                    <label for="coordenadas">Coordenadas</label>
                    <div class="col-md-12 col-sm-12" id="map-canvas" style="height:500px;"></div>
                </div>



       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-danger" href="{{url('/obra_lugar')}}/ligar/{{$obra->id_obra}}">Cancelar</a>
            </div>
        {!!Form::close()!!}

<script>
        var map;
        function initialize() {
        map = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: 3,
            center: {lat: -10, lng: -60}
        });
        
        var marker=new google.maps.Marker({
            position:map.getCenter(), 
            map:map, 
            draggable:true
        });
            google.maps.event.addListener(marker,'dragend',function(event) {
            document.getElementById("latitud").value = this.getPosition().lat().toString();
            document.getElementById("longitud").value = this.getPosition().lng().toString();
        });
        }
        google.maps.event.addDomListener(window, 'load', initialize);
</script>


    </div>
</div>
@endsection