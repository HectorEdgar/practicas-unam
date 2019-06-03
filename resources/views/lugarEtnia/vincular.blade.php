<!--Esta es la vista que crea un nuevo etnia
y lo liga automaticamente con el lugar -->

@extends('layouts.admin') 
@section('titulo')
    Vincular Lugar con Etnia
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/lugar')}}">Lugares</a></li>
                <li class="breadcrumb-item"><a href="{{url('/lugar')}}/{{$lugar->id_lugar}}">Resumen Lugar. ({{ $lugar->id_lugar}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/lugar_etnia')}}/ligar/{{$lugar->id_lugar}}">Administrar Lugar y Etnia</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vincular</li>
                </ol>
        </nav>

        <div class="text-center">
            <p>Se vinculará la Etnia: <strong>({{$etnia->id_etnia}})</strong> {{$etnia->nombre}}
            con el Lugar: <strong>({{$lugar->id_lugar}})</strong> {{$lugar->ubicacion}}</p>
        </div>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Lugaretnia-->
        
{!!Form::open(array('url'=>'lugar_etnia','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
                {{Form::token()}}  
                <input type="hidden" name="fk_lugar" class="form-control" value="{{$lugar->id_lugar}}">
                <input type="hidden" name="fk_etnia" class="form-control" value="{{$etnia->id_etnia}}">
            <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="Latitud">Latitud:</label>
                    <input class="form-control" type="text" style="width:400px" id="latitud" name="latitud" />
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                 <div class="form-group">
                    <label for="Longitud">Longitud:</label>
                    <input class="form-control" type="text" style="width:400px" id="longitud" name="longitud" />
                </div>
            </div>
           
           
        </div>
                <div class="form-group">
                    <label for="coordenadas">Coordenadas</label>
                    <div class="col-md-12 col-sm-12" id="map-canvas" style="height:500px;"></div>
                </div>

                <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-danger" href="{{url('/lugar_etnia')}}/ligar/{{$lugar->id_lugar}}">Cancelar</a>
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
