@extends('layouts.admin')
@section('titulo')
@endsection
@section('contenido')

@if(Session::has('flash_message'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('flash_message')}}
</div>
@endif

@if(Session::has('flash_message2'))
<div class="alert alert-warning">
  <strong>¡Éxito!</strong> {{Session::get('flash_message2')}}
</div>
@endif

@if(Session::has('flash_message3'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('flash_message3')}}
</div>
@endif

@if(Session::has('flash_message4'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong>{{Session::get('flash_message4')}}
</div>
@endif

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Administrar Documento y Lugar</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Lugares vinculados<br> 
                 
                    Id del documento : ( {{$documento->Id_doc}}  )
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
    </div>

    <!-- Tabla de lugares agregados -->
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-light" style="background-color: green">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Ubicación</th>
                        <th scope="col" class="text-center align-middle">País</th>
                        <th scope="col" class="text-center align-middle">Región G.</th>
                        <th scope="col" class="text-center align-middle ">Eliminar</th>
                      
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($lugaresdelDocumento as $item)
                        <tr>
                           <th scope="row"  class="text-center align-middle">{{$item ->id_lugar}}</td>
                            <td class="text-center align-middle">{{$item ->ubicacion}}</td>
                            <td class="text-center align-middle">{{$item ->pais}}</td>
                            <td class="text-center align-middle">{{$item ->region}}</td>
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal2-delete-{{$item->id_lugar}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                            </a>
                            </td>
                        </tr>
                    @include('documentoLugar.modal2')
                    @endforeach
                </tbody>
            </table>

<center><h3>Vincular Lugar existente</h3></center>
        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('documentoLugar.search')
            </div>
            <!--Etiquetas de la imagen de agregar-->
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <a href="{{URL::action('DocumentoLugarController@nuevoLugarDocumento',$documento->Id_doc)}}" class="btn btn-outline-success col-sm-12 col-xs-12" >
                    <img width="30px" src="{{asset('imgs/agregar.svg')}}"></img>
                </a>
            </div>
           
        </div>



        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-dark">
                    <tr>
                         <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Ubicación</th>
                        <th scope="col" class="text-center align-middle">País</th>
                        <th scope="col" class="text-center align-middle">Región G.</th>
                        <th scope="col" class="text-center align-middle ">Vincular</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($lugares as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->id_lugar}}</td>
                            <td class="text-center align-middle">{{$item ->ubicacion}}</td>
                            <td class="text-center align-middle">{{$item ->pais}}</td>
                            <td class="text-center align-middle">{{$item ->region}}</td>
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal-delete-{{$item->id_lugar}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/link.svg')}}" title="Vincular"></img>
                            </a>
                            </td>
                        </tr>
                    @include('documentoLugar.modal')
                    @endforeach
                </tbody>
            </table>
           
            @include('documentoLugar.paginador')
        </div>

@endsection