@extends('layouts.admin')
@section('titulo')
    Index de log
@endsection

@section('contenido')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('LogController@index')}}">Log</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Logs
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('log.search')
            </div>
        </div>
        
        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
            <table class="table table-hover table-sm " style="table-layout:fixed;">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Tipo de cambio</th>
                        <th scope="col" class="text-center align-middle">Tabla</th>
                        <th scope="col" class="text-center align-middle">Usuario</th>
                        <th scope="col" class="text-center align-middle">Descripción</th>
                        <th scope="col" class="text-center align-middle">Fecha de creación</th>                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->idLog}}</td>
                            <td class="text-center align-middle">{{$item ->tipocambio->tipoCambio}}</td>
                            <td class="text-center align-middle" style="word-wrap:break-word">{{$item ->tabla}}</td>
                            <td class="text-center align-middle">{{$item->usuario->name}}</td>
                            <td class="text-center align-middle" style="word-wrap:break-word"><p class="">{{$item ->descripcion}}</p></td>
                            <td class="text-center align-middle">{{$item ->fechaCreacion}}</td>                            
                        </tr>
                        <!--c@include('log.modal')--> 
                    @endforeach
                </tbody>
            </table>
            @include('log.paginador')
        </div>

@endsection