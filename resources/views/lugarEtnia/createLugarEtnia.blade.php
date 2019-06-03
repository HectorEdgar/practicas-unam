<!--Esta es la vista que crea un nuevo etnia
y lo liga automaticamente con el lugar -->

@extends('layouts.admin') 
@section('titulo')
    Crear Etnia
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
                <li class="breadcrumb-item active" aria-current="page">Crear Etnia</li>
                </ol>
        </nav>
        <h3>Nueva Etnia</h3>
        <h5>Esta nueva etnia se ligará automáticamente con el lugar ({{$lugar->id_lugar}})</h5>

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
      {!!Form::model($lugar,['method'=>'PATCH','route'=>['lugar_etnia.update',$lugar->id_lugar]]) !!}
        {{Form::token()}}
      <div class="form-group">
            <label for="nombre">Grupo Étnico</label>
            <input type="text" name="nombre" class="form-control" placeholder="Grupo Étnico...">
        </div>
        <div class="form-group">
            <label for="nombre2">Otros Nombres</label>
            <input type="text" name="nombre2" class="form-control" placeholder="Otros Nombres...">
        </div>
        <div class="form-group">
            <label for="familia">Familia Lingüística</label>
            <input type="text" name="familia" class="form-control" placeholder="Familia Lingüística...">
        </div>
        <div class="form-group">
            <label for="territorio">Territorio Indígena</label>
            <input type="text" name="territorio" class="form-control" placeholder="Territorio Indígena...">
        </div>
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