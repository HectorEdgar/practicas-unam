@extends('layouts.admin')
@inject('service', 'sistema\Http\Controllers\DocumentoController')
@section('titulo')
    Área de Consultas

@endsection

@section('contenido')

<h1>Área de Consultas</h1>
<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{url('/consultas')}}">Área de Consulta</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Consulta Id</li>
                </ol>
            </nav>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
</div>


<h3>Resultados de la Búsqueda</h3>
<h5 class="text-center"><strong>ID: ({{$documento->Id_doc}}) </strong>{{$documento->titulo}}</h5>

<div class="card text-center ">

    <div class="card text-center ">
        <div class="card-header">


              <div class="row">

              <div class="col-md-5 col-sm-4 col-0">  </div> <div class="col-md-6  col-sm-7 col-10 text-left">     <h4 class="text-left" >Referencia  </h4> </div>

                    <button class="btn btnCopia col-md-1  col-sm-1 col-2" data-clipboard-target="div#referencia">

                          <img  src="{{asset('imgs/clippy.svg')}}" alt="Copiar al portapapeles" width="15" height="15" data-clipboard-action="copy">

                    </button>

                </div>

        </div>
        <div class="card-body" >

              @if ($documento->url != "" && $documento->derecho_autor == 1)
              <div id="referencia">
           @php
           echo $referencia
           @endphp
              <br/>
              <br/>

              @if ($documento->tipo !=16)
              <h6>Consultado en: {{$documento->url or 'Sin url'}}</h6>
              <h6>Fecha de Consulta: {{$documento->fecha_consulta or 'Sin fecha'}}</h6>
              </div>
              @endif

              @elseif ($documento->url != "" && $documento->derecho_autor == 0)
              <div id="referencia">
                @php
                echo $referencia
                @endphp
                    <br/>
                    <br/>
              @if ($documento->tipo !=16)
              <h6>Consultado en: {{$documento->url or 'Sin url'}}</h6>
              <h6>Fecha de Consulta: {{$documento->fecha_consulta or 'Sin fecha'}}</h6>
              </div>
              @endif

            </div>

            @elseif ($documento->url == "" && $documento->derecho_autor == 1)
            <div id="referencia">

                  @php
                  echo $referencia
                  @endphp

            </div>


            <br/>
            <br>

            @elseif ($documento->url== "" && $documento->derecho_autor == 0)

            <div id="referencia">

                  @php
                  echo $referencia
                  @endphp

            </div>


            <br/> Disponible en: Programa Universitario de Estudios de la Diversidad Cultural y la Interculturalidad de la UNAM oficina Oaxaca.
            <br>
             @endif





      </div>
        <div class="card-footer" style="color:ghostwhite">
                <a  href="{{URL::action('DocumentoController@show',$documento->Id_doc)}} " class="btn btn-secondary">Ir al detalle del documento</a>
          </div>
</div>


<script src="{{asset('js/clipboard.min.js')}}" ></script>

<script type="text/javascript">

  var clipboard = new ClipboardJS('.btnCopia');
    clipboard.on('success', function(e) {
      //e.clearSelection();
      console.log(e);

    });

    clipboard.on('error', function(e) {
        console.log(e);
    });


</script>


@endsection