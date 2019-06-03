@extends('layouts.admin')
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
                    <li class="breadcrumb-item"><a href="{{url('/consultas')}}">Consultas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Resultados de la Consulta</li>
                </ol>
            </nav>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
</div>

<h4 class="text-center">Consulta Por Estatus de Revisión:  
    
        @if ($status==0)
                           
        <span style="color:red"><strong>No Revisado</strong></span>
        
        @else
        <span style="color:green"><strong>Revisado</strong></span>                      
        @endif
    <strong></strong></h4>

    
    <!--Etiquetas del formulario de busqueda-->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @include('consultas.searchEstatus')
        </div>
    </div>
    
    <br>
<!---->

    <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center align-middle">Id</th>
                                <th scope="col" class="text-center align-middle">Título</th>
                                <th scope="col" class="text-center align-middle">Estatus</th>

                                <th scope="col" class="text-center align-middle">Ver</th>
                            </tr>
        
                        </thead>
                       @foreach ($documentos as $doc)
                        <tr>
                            <th scope="row" class="text-center align-middle">{{ $doc->Id_doc}}</th>
                            <th scope="row" class="text-center align-middle">{{ $doc->titulo}}</th>
                            <th scope="row" class="text-center align-middle">

                            @if ($doc ->revisado==0)
                           
                            <div style="color:red">No Revisado</div>
                            
                            @else
                            <div style="color:green">Revisado</div>                      
                            @endif
                           </th>
                            <td class="text-center align-middle">
                                  
                                    <a href="{{URL::action('DocumentoController@show',$doc->Id_doc)}}">
                                    <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                    </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @include('consultas.paginador2')

@endsection