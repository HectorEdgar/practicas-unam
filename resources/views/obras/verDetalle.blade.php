@extends ('layouts.admin') @section ('contenido')

<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
            <li class="breadcrumb-item">
                  <a href="{{url('/')}}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                  <a href="{{url('/obras')}}">Obra</a>
            </li>
            <li class="breadcrumb-item active">
                  <a href="#">Ver Detalle</a>
            </li>
      </ol>
</nav>
<div class="text-center">
<h1> Resumen de {{$tipo}}</h1> 
<h5>({{$obra->id_obra}}) {{$obra->nombre}}</h5>
</div>
<br>
<ul class="nav nav-pills nav-justified container-fluid" id="mistabs" role="tablist">
      <li class="nav-item mr-1">
                        <button class="btn btn-primary container-fluid" id="docs-tab" data-toggle="tab" href="#docs" role="tab" aria-controls="docs"
                              aria-selected="false">Documentos</button>
      </li>
      <li class="nav-item mr-1">
            <button  style="background-color:orange; border:none" class="btn btn container-fluid" id="ejes-tab" data-toggle="tab" href="#ejes" role="tab" aria-controls="ejes"
                  aria-selected="false">Ejes</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-danger container-fluid" id="ubicacion-tab" data-toggle="tab" href="#ubicacion" role="tab" aria-controls="ubicacion"
                  aria-selected="false">Ubicación</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-info container-fluid" id="institucion-tab" data-toggle="tab" href="#institucion" role="tab" aria-controls="institucion"
                  aria-selected="false">Instituciones</button>
      </li>
        <li class="nav-item mr-1">
            <button class="btn btn-success container-fluid" id="personas-tab" data-toggle="tab" href="#personas" role="tab" aria-controls="personas"
                  aria-selected="false">Actores Sociales</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-warning container-fluid" id="temas-tab" data-toggle="tab" href="#temas" role="tab" aria-controls="temas" aria-selected="false">Temas</button>
      </li>
      <li class="nav-item mr-1">
            <button style="background-color:#FA8072; border:none" class="btn btn-info container-fluid" id="proyectos-tab" data-toggle="tab" href="#proyectos" role="tab" aria-controls="proyectos"
                  aria-selected="false">Proyectos</button>
      </li>
       <li class="nav-item mr-1">
            <button style="background-color:#7E5DBC; border:none" class="btn btn-info container-fluid" id="obras-tab" data-toggle="tab" href="#obras" role="tab" aria-controls="obras"
                  aria-selected="false">Obras</button>
      </li>
</ul>
<div class="tab-content" id="myTabContent">
      @include('obras.verDetalleDocumentos')      
      @include('obras.verDetalleEje')
      @include('obras.verDetalleUbicacion')
      @include('obras.verDetalleInstitucion')
      @include('obras.verDetallePersona')
      @include('obras.verDetalleTema')
      @include('obras.verDetalleProyecto')
      @include('obras.verDetalleObra')
</div>
@endsection