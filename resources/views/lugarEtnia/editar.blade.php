<!--Esta es la vista que crea un nuevo etnia
y lo liga automaticamente con el lugar -->

@extends('layouts.admin') 
@section('titulo')
    Editar Vinculo Lugar - Etnia
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
                    <li class="breadcrumb-item"><a href="{{url('/lugar')}}">Lugares</a></li>
                <li class="breadcrumb-item"><a href="{{url('/lugar')}}/{{$lugar->id_lugar}}">Resumen Lugar. ({{ $lugar->id_lugar}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/lugar_etnia')}}/ligar/{{$lugar->id_lugar}}">Administrar Lugar y Etnia</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Vínculo</li>
                </ol>
        </nav>
        <h3>Editar Vínculo Lugar - Etnia</h3>
        <h5>{{$lugar->nombre}}</h5>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR Lugar etnia-->
{!! Form::open(['action' => ['LugarEtniaController@editarVinculo', $lugar->id_lugar], 'method' =>'POST']) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="nombre">Grupo Étnico</label>
       <input type="text" name="nombre" class="form-control" placeholder="Grupo Étnico..." value="{{$etnia->nombre}}">
        </div>
        <div class="form-group">
            <label for="nombre2">Otros Nombres</label>
            <input type="text" name="nombre2" class="form-control" placeholder="Otros Nombres..." value="{{$etnia->nombre2}}">
        </div>
        <div class="form-group">
            <label for="familia">Familia Lingüística</label>
            <input type="text" name="familia" class="form-control" placeholder="Familia Lingüística..." value="{{$etnia->familia}}">
        </div>
        <div class="form-group">
            <label for="territorio">Territorio Indígena</label>
            <input type="text" name="territorio" class="form-control" placeholder="Territorio Indígena..." value="{{$etnia->territorio}}">
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

             <input type="hidden" name="fk_lugar" class="form-control" value="{{$vinculo->fk_lugar}}">
             <input type="hidden" name="fk_etnia" class="form-control" value="{{$vinculo->fk_etnia}}">



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