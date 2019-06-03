@extends ('layouts.admin') @section ('contenido')

<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
            <li class="breadcrumb-item">
                  <a href="{{url('/')}}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                  <a href="{{url('/persona')}}">Persona</a>
            </li>
            <li class="breadcrumb-item active">
                  <a href="#">Ver Detalle</a>
            </li>
      </ol>
</nav>
<div class="text-center">
<h1>Resumen de la Persona:</h1> 
<h5><strong>({{$persona->Id_persona}})</strong> {{$persona->nombre}} {{$persona->apellidos}}</h5>
</div>
<br>
<ul class="nav nav-pills nav-justified container-fluid" id="mistabs" role="tablist">
      <li class="nav-item mr-1">
            <button class="btn btn-primary container-fluid" id="docs-tab" data-toggle="tab" href="#docs" role="tab" aria-controls="docs"
                  aria-selected="false">Documentos</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-danger container-fluid" id="ubicacion-tab" data-toggle="tab" href="#obras" role="tab" aria-controls="obras"
                  aria-selected="false">Obras</button>
      </li>
      
</ul>
<div class="tab-content" id="myTabContent">
 @include('persona.verDetalleDocumento')
 @include('persona.verDetalleObra')


</div>
@endsection