@extends ('layouts.admin')
@section('titulo')
    Detalles Documento
@endsection
@section ('contenido')

<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
            <li class="breadcrumb-item">
                  <a href="{{url('/')}}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                  <a href="{{url('/documento')}}">Documento</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                  <a href="">Ver Detalle</a>
            </li>
      </ol>
</nav>





<hr/>
<h1 class="text-center"> Resumen del Documento </h1>
<h5 class="text-center"> ( {{$documento->Id_doc}} ) {{$documento->titulo}}</h5>
<hr/>
<br>

<!-- Referencia  -->


<div class="card text-center ">
      <div class="card-header">


            <div class="row">

            <div class="col-md-5 col-sm-4 col-0">  </div> <div class="col-md-6  col-sm-7 col-10 text-left">  <h4 class="text-left" >Referencia  </h4> </div>

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
            <h6>Fecha de consulta: {{$documento->fecha_consulta or 'Sin fecha'}}</h6>
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
            <h6>Fecha de consulta: {{$documento->fecha_consulta or 'Sin fecha'}}</h6>
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


      <div class="card-footer text-muted">
      
 
           <a class="btn btn-secondary" href="{{URL::action('DocumentoController@verPdf',$documento->Id_doc)}}" target="_blank">Ir al documento</a>

            <a class="btn btn-warning" href="{{URL::action('DocumentoController@edit',$documento->Id_doc)}}">
                  Editar Documento
                  </a>

            <a class="btn btn-danger" href="" data-target="#modal-delete-{{$documento->Id_doc}}" data-toggle="modal">
                  Eliminar Documento
            </a>

      </div>
</div>





<br>
<br>
<ul class="nav nav-pills nav-justified container-fluid" id="mistabs" role="tablist">
      <li class="nav-item mr-1">
            <button class="btn btn-primary container-fluid" id="generales-tab" data-toggle="tab" href="#generales" role="tab" aria-controls="generales"
                  aria-selected="true">Generales</button>
      </li>

      <li class="nav-item mr-1">
            <button class="btn btn-outline-success container-fluid" id="actorsocial-tab" data-toggle="tab" href="#actorsocial" role="tab"
                  aria-controls="actorsocial" aria-selected="false">Actores Sociales</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-outline-danger container-fluid" id="instituciones-tab" data-toggle="tab" href="#instituciones" role="tab"
                  aria-controls="instituciones" aria-selected="false">Instituciones</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-outline-warning container-fluid" id="temas-tab" data-toggle="tab" href="#temas" role="tab" aria-controls="temas"
                  aria-selected="false">Temas</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-outline-info container-fluid" id="lugares-tab" data-toggle="tab" href="#lugares" role="tab" aria-controls="lugares"
                  aria-selected="false">Lugares</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-outline-dark container-fluid" id="subtemas-tab" data-toggle="tab" href="#subtemas" role="tab" aria-controls="subtemas"
                  aria-selected="false">Subtemas</button>
      </li>

      <li class="nav-item mr-1">
            <button class="btn btn-outline-primary container-fluid" id="obras-tab" data-toggle="tab" href="#obras" role="tab" aria-controls="obras"
                  aria-selected="false">Obras</button>
      </li>
</ul>

<div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="generales" role="tabpanel" aria-labelledby="generales-tab">
            <br>
            <table class="table table-bordered">

                  <!-- TODAS LAS VARIABLES QUE SE IMPRIMEN ESTAN MANEJADAS EN EL CONTROLADOR DE DOCUMENTO -->
                  <tr>
                        <th>ID de Documento</th>
                        <td> {{$documento->Id_doc}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Título</th>
                        <td> {{$documento->titulo}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Tipo de Documento</th>
                        <td>{{$tipo}}
                              <td></td>

                  </tr>
                  <tr>
                        <th>Lugar de Publicación</th>
                        <td>{{$lugarPublicacion}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Derechos de autor</th>
                        <td>{{$derechos}}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Investigador</th>
                        <td>
                              <strong> {{$documento->investigador}} </strong>
                        </td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>URL</th>
                        <td>
                              <a href="{{$documento->url}}">{{ $documento->url}}</a>
                        </td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Autor(es)</th>
                        @if (count($autores)==0)
                        <td>- - - - - - - -</td>
                        @else
                        <td>
                              @foreach ($autores as $autor) ( {{$autor->Id_autor}}) {{$autor->pseudonimo}} {{$autor->nombre}} {{$autor->apellidos}}
                              <br> @endforeach
                        </td>
                        @endif
                        <td>
                              <a class="btn btn-primary" href="{{URL::action('DocumentoAutorController@ligarDocumento',$documento->Id_doc)}}">Administrar
                                    <br>Autores</a>
                        </td>
                  </tr>

                  <tr>
                        <th>Editor</th>
                        @if (count($editores)==0)
                        <td>- - - - - - - -</td>
                        @else
                        <td>
                              @foreach ($editores as $editor) ( {{$editor->id_editor}}) {{$editor->editor}}
                              <br> @endforeach
                        </td>
                        @endif
                        <td>
                              <a class="btn btn-primary" href="{{URL::action('DocumentoEditorController@ligarDocumento',$documento->Id_doc)}}">Administrar
                                    <br>Editores</a>
                        </td>

                  </tr>
                  <tr>
                        <th>Fecha de Publicación</th>
                        <td> 
                              @php

                              if($fecha!=null){
                               echo('<b>'.$fecha.'</b>');
                              }else if($fechaExtra!=null){
                                    echo('De <b>'.$fechaExtra->mes.'</b> a <b>'.$fechaExtra->mes2.'</b> de <b>'. $fechaExtra->anio. '</b>');
                              }

                              
                              @endphp
                              
                        </td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Fecha de Consulta</th>
                        <td> {{$documento->fecha_consulta}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Fecha de Registro</th>
                        <td> {{$documento->fecha_registro}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Notas</th>
                        <td> {{$notas}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Población</th>
                        <td>{{$poblacion}}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Proyecto</th>
                        @if ($proyectos=='')
                        <td>- - - - - - - -</td>
                        @else
                        <td>
                              @foreach ($proyectos as $pro) {{$pro->proyecto}}
                              <br> @endforeach
                        </td>
                        @endif
                        <td>
                              <a class="btn btn-primary" href="{{URL::action('DocumentoProyectoController@ligarDocumento',$documento->Id_doc)}}">Administrar
                                    <br>Proyectos</a>
                        </td>
                  </tr>
                  <tr>
                        <th>Estado de Revisión</th>
                        <td> {{$revisado}} </td>
                        @if(Auth::user()->hasAnyRole(['admin','revisor']))
                        @if ($documento ->revisado==0)
                        <td>
                              <a class="btn btn-success" href="{{action('DocumentoController@cambiarEstadoRevision',$documento->Id_doc)}}">Cambiar a
                                    <br/>a revisado </a>
                        </td>
                        @else

                        <td>
                              <a class="btn btn-danger" href="{{action('DocumentoController@cambiarEstadoRevision',$documento->Id_doc)}}">Cambiar a
                                    <br/>No revisado </a>
                        </td>
                        @endif

                        @endif






                  </tr>
                  <tr>
                        <th>Publicado</th>
                        <td> {{$linea}}</td>
                        <td></td>
                  </tr>

            </table>

            <br>
            <hr>
            <h4 class="text-center">Tipo documento</h4>
            <hr>

            <br>
            <!--    Tabla segun el tipo  -->
            <table class="table table-bordered tipoDocumento" id="tablaRevista_boletin">



                  <tr>
                        <th>Nombre revista o Boletín</th>
                        <td> {{$tipoDocumento->nombre_revista or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Número </th>
                        <td> {{$tipoDocumento->num_revista or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Volumen</th>
                        <td> {{$tipoDocumento->volumen or '' }}</td>

                        <td></td>
                  </tr>
                  <tr>
                        <th>Páginas</th>
                        <td> {{$tipoDocumento->pag or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Año</th>
                        <td> {{$tipoDocumento->anio or '' }}</td>
                        <td></td>
                  </tr>


            </table>


            <table class="table table-bordered tipoDocumento" id="tablaLibro">



                  <tr>
                        <th>Responsable del Prólogo:</th>
                        <td> {{$tipoDocumento->prologo or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th> Nombre del Traductor</th>
                        <td> {{$tipoDocumento->traductor or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th> Responsable de la Introducción</th>
                        <td> {{$tipoDocumento->introduccion or '' }}</td>

                        <td></td>
                  </tr>
                  <tr>
                        <th>Páginas</th>
                        <td> {{$tipoDocumento->paginalib or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Edición</th>
                        <td> {{$tipoDocumento->edicion or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Tomos</th>
                        <td> {{$tipoDocumento->tomos or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Serie</th>
                        <td> {{$tipoDocumento->serie or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Volumen</th>
                        <td> {{$tipoDocumento->volumen or '' }}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Colección</th>
                        <td> {{$tipoDocumento->coleccion or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th> No. de Colección</th>
                        <td> {{$tipoDocumento->nocol or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th> No. de Serie</th>
                        <td> {{$tipoDocumento->noserie or '' }}</td>
                        <td></td>
                  </tr>


            </table>



            <table class="table table-bordered  tipoDocumento" id="tablaCapLibro">



                  <tr>
                        <th>Nombre del Libro</th>
                        <td> {{$tipoDocumento->nombre_libro or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Autor General</th>
                        <td> {{$tipoDocumento->autorgral or '' }}</td>
                        <td></td>

                  </tr>


                  <tr>
                        <th>Nombre del Traductor</th>
                        <td> {{$tipoDocumento->traductor or '' }}</td>

                        <td></td>
                  </tr>
                  <tr>
                        <th>Páginas</th>
                        <td> {{$tipoDocumento->paginas or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Edición</th>
                        <td> {{$tipoDocumento->edicion or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Tomos</th>
                        <td> {{$tipoDocumento->tomos or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Serie</th>
                        <td> {{$tipoDocumento->serie or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Volumen</th>
                        <td> {{$tipoDocumento->volumen or '' }}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Colección</th>
                        <td> {{$tipoDocumento->coleccion or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th> No. de Colección</th>
                        <td> {{$tipoDocumento->nocol or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th> No. de Serie</th>
                        <td> {{$tipoDocumento->noserie or '' }}</td>
                        <td></td>
                  </tr>


            </table>


            <table class="table table-bordered tipoDocumento " id="tablaPonencias">



                  <tr>
                        <th> Nombre del evento</th>
                        <td> {{$tipoDocumento->evento or '' }}</td>
                        <td></td>
                  </tr>

                  <tr>
                        <th>Lugar evento</th>
                        <td> {{$tipoDocumento->lugar_presentacion or '' }}</td>
                        <td></td>

                  </tr>


                  <tr>
                        <th>Fecha del evento</th>
                        <td> {{$tipoDocumento->fecha_pesentacion or '' }}</td>

                        <td></td>
                  </tr>
                  <tr>
                        <th>Páginas</th>
                        <td> {{$tipoDocumento->paginas or '' }}</td>
                        <td></td>

                  </tr>
            </table>




            <table class="table table-bordered tipoDocumento " id="tablaTesis">



                  <tr>
                        <th>Asesor</th>
                        <td> {{$tipoDocumento->asesor or '' }}</td>
                        <td></td>
                  </tr>
                  <tr>
                        <th>Grado</th>
                        <td> {{$tipoDocumento->grado or '' }}</td>
                        <td></td>

                  </tr>
                  <tr>
                        <th>Páginas</th>
                        <td> {{$tipoDocumento->num_paginas or '' }}</td>
                        <td></td>

                  </tr>
            </table>




      </div>
      @include('documento.verDetallePersona') @include('documento.verDetalleTema') @include('documento.verDetalleLugar') @include('documento.verDetalleInstitucion')
      @include('documento.verDetalleSubtema') @include('documento.verDetalleObra')




      <div class="modal fade" id="modal-delete-{{$documento->Id_doc}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            {{Form::Open(array('action'=>array('DocumentoController@destroy',$documento->Id_doc),'method'=>'delete'))}}
            <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                        <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLongTitle">¿Eliminar Documento?</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                        </div>
                        <div class="modal-body">
                              <p>Confirme si desea Eliminar el Documento {{$documento->Id_doc}}</p>
                        </div>
                        <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                              <button type="submit" class="btn btn-danger">Confirmar</button>
                        </div>
                  </div>
            </div>
            {{Form::Close()}}
      </div>


</div>



<script src="{{asset('js/clipboard.min.js')}}" ></script>

<script type="text/javascript">
      $(document).ready(function () {



            var valorSeleccionado = parseInt('<?php echo $documento->tipo;?>');



            switch (valorSeleccionado) {
                  case 2:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaRevista_boletin").attr("hidden", false);
                        break;
                  case 8:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaLibro").attr("hidden", false);
                        break;
                  case 10:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaPonencias").attr("hidden", false);
                        break;
                  case 13:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaTesis").attr("hidden", false);
                        break;
                  case 14:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaRevista_boletin").attr("hidden", false);
                        break;
                  case 15:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaCapLibro").attr("hidden", false);
                        break;
                  case 17:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaRevista_boletin").attr("hidden", false);
                        break;
                  case 18:
                        $('.tipoDocumento').attr("hidden", true);
                        $("#tablaRevista_boletin").attr("hidden", false);
                        break;

                  default:
                        $('.tipoDocumento').attr("hidden", true);
                        break;
            }



      });


//////

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