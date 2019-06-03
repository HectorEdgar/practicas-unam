@extends ('layouts.admin') @section ('contenido')

<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
            <li class="breadcrumb-item">
                  <a href="{{url('/')}}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                  <a href="{{url('/subtema')}}">Subtema</a>
            </li>
            <li class="breadcrumb-item active">
                  <a href="#">Ver Detalle</a>
            </li>
      </ol>
</nav>
<div class="text-center">
<h1> Resumen del subtema:</h1> 
<h5><strong>({{$subtema->id_sub}})</strong> {{$subtema->subtema}}</h5>
</div>
<br>
<ul class="nav nav-pills nav-justified container-fluid" id="mistabs" role="tablist">
      <li class="nav-item mr-1">
            <button class="btn btn-primary container-fluid" id="docs-tab" data-toggle="tab" href="#docs" role="tab" aria-controls="docs"
                  aria-selected="false">Documentos</button>
      </li>
     
      
</ul>
<div class="tab-content" id="myTabContent">
 @include('subtema.verDetalleDocumento')



</div>
@endsection