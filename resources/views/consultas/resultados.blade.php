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
                    <li class="breadcrumb-item active" aria-current="page">Consulta</li>
                </ol>
            </nav>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
</div>

<h1>Consulta por Tipo de Documento:
    <strong>{{$tipoConsulta}}</strong></h1>


    <!--Etiquetas del formulario de busqueda-->
    <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
             @include('consultas.search')
         </div>
     </div>

     <br>

<div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">

        <table class="table table-hover table-sm ">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="text-center align-middle">Id</th>
                            <th scope="col" class="text-center align-middle">Referencia</th>
                            <th scope="col" class="text-center align-middle">Ver</th>
                        </tr>

                    </thead>
                    @for ($i = 0; $i < count($documentos); $i++)
                    <tr>
                        <th scope="row" class="text-center align-middle">{{ $documentos[$i]->Id_doc}}</th>
                        <td class="text-center align-middle">
                                @php
                                echo $referencia[$i]
                                @endphp
                        </td>

                        <td class="text-center align-middle">

                                <a href="{{URL::action('DocumentoController@show', $documentos[$i]->Id_doc)}}">
                                <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                </a>
                        </td>
                    </tr>
                    @endfor
                </table>
                @include('consultas.paginador')
@endsection