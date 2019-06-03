<!--Esta es la vista que crea un nuevo lugar
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Editar Vinculo Obra - Lugar
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
                    <li class="breadcrumb-item"><a href="{{url('/obra')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_lugar')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Lugar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Vínculo</li>
                </ol>
        </nav>
        <h3>Editar vínculo Obra - Lugar</h3>
        <h5>{{$obra->nombre}}</h5>
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
{!! Form::open(['action' => ['ObraLugarController@editarVinculo', $obra->id_obra], 'method' =>'POST']) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="ubicacion">Ubicación</label>
       <input type="text" name="ubicacion" class="form-control" placeholder="Ubicación..." value="{{$lugar->ubicacion}}">
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
                        @if($item->id_pais==$lugar->pais)
                        <option value="{{$item->id_pais}}" selected>{{$item->nombre}}</option>
                        @else
                        <option value="{{$item->id_pais}}">{{$item->nombre}}</option>
                        @endif
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
                    @foreach($regiones as $item)
                    @if($item->id_region==$lugar->region_geografica)
                    <option value="{{$item->id_region}}" selected>{{$item->nombrereg}}</option>
                    @else 
                    <option value="{{$item->id_region}}">{{$item->nombrereg}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>

<div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="Latitud">Latitud:</label>
                <input class="form-control" type="text" style="width:400px" id="latitud" name="latitud" value="{{$vinculo->latitud}}"/>
                </div>
                <div class="form-group">
                    <label for="Longitud">Longitud:</label>
                    <input class="form-control" type="text" style="width:400px" id="longitud" name="longitud" value="{{$vinculo->longitud}}" />
                </div>

             <input type="hidden" name="fk_obra" class="form-control" value="{{$vinculo->fk_obra}}">
             <input type="hidden" name="fk_lugar" class="form-control" value="{{$vinculo->fk_lugar}}">



            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="complejo">Complejo</label>
                    <select class="form-control selectpicker" data-live-search="true" name="complejo">
                        @if($vinculo->complejo==0)
                        <option data-tokens="- - - - " value="0" selected>- - Sin Complejo - -</option>
                        @else
                        <option data-tokens="- - - - " value="0">- - Sin Complejo - -</option>
                        @endif
                        @foreach($complejos as $item)
                        @if($item->id_obra == $vinculo->complejo)
                        <option data-tokens="{{$item->nombre}}" selected value="{{$item->id_obra}}">{{$item->nombre}}</option>
                        @else
                        <option data-tokens="{{$item->nombre}}" value="{{$item->id_obra}}">{{$item->nombre}}</option>
                        @endif
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
            	<button class="btn btn-danger" type="reset">Cancelar</button>
            </div>
        {!!Form::close()!!}

<script>
        var map;
        var latitud2 =parseInt(document.getElementById("latitud").value);
        var longitud2 =parseInt(document.getElementById("longitud").value);
        
        function initialize() {
        map = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: 3,
            center: {lat: latitud2, lng: longitud2}
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