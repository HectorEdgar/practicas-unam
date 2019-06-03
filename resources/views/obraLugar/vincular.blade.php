<!--Esta es la vista que crea un nuevo lugar
y lo liga automaticamente con el obra -->

@extends('layouts.admin') 
@section('titulo')
    Vincular Obra con Lugar
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra. ({{ $obra->id_obra}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obra_lugar')}}/ligar/{{$obra->id_obra}}">Administrar Obra y Lugar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vincular</li>
                </ol>
        </nav>

        <div class="text-center">
            <p>Se vinculará el Lugar: <strong>({{$lugar->id_lugar}})</strong> {{$lugar->ubicacion}}
            con la Obra: <strong>({{$obra->id_obra}})</strong> {{$obra->nombre}}</p>
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

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Obralugar-->
        
{!!Form::open(array('url'=>'obra_lugar','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
                {{Form::token()}}  
                <input type="hidden" name="fk_obra" class="form-control" value="{{$obra->id_obra}}">
                <input type="hidden" name="fk_lugar" class="form-control" value="{{$lugar->id_lugar}}">
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
                    <div class="col-md-12 col-sm-12" id="map-canvas" style="height:200px;"></div>
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
