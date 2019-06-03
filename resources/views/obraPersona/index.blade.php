@extends('layouts.admin')
@section('titulo')
@endsection
@section('contenido')

@if(Session::has('flash_message'))
<div class="alert alert-success">
  <strong>¡Éxito! </strong> {{Session::get('flash_message')}}
</div>
@endif

@if(Session::has('flash_message2'))
<div class="alert alert-warning">
  <strong>¡Aviso!</strong> {{Session::get('flash_message2')}}
</div>
@endif

@if(Session::has('flash_message3'))
<div class="alert alert-success">
  <strong>¡Éxito! </strong> {{Session::get('flash_message3')}}
</div>
@endif

@if(Session::has('flash_message4'))
<div class="alert alert-success">
  <strong>¡Éxito! </strong>{{Session::get('flash_message4')}}
</div>
@endif
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra ({{ $obra->id_obra}})</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Administrar Obra y Personas</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Personas vinculadas<br> 
                 
                    ID de la obra : ( {{$obra->id_obra}}  )
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
    </div>

    <!-- Tabla de personas agregados -->
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-light" style="background-color: green">
                    <tr>
                       <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Apellidos</th>
                        <th scope="col" class="text-center align-middle">Cargo</th>
                        <th scope="col" class="text-center align-middle">Sector</th>
                        <th scope="col" class="text-center align-middle ">Eliminar</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($personasdelaObra as $item)
                        <tr>
                           <th scope="row"  class="text-center align-middle">{{$item ->Id_persona}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">{{$item ->apellidos}}</td>
                            <td class="text-center align-middle">{{$item ->cargo}}</td>
                            <td class="text-center align-middle">
                                @if ($item ->extra==1)
                                Social
                                @elseif ($item ->extra==2)
                                Institucional/Gubernamental
                                @else
                                --------
                                @endif
                            
                            </td>
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal2-delete-{{$item->Id_persona}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                            </a>
                            </td>
                        </tr>
                    @include('obraPersona.modal2')
                    @endforeach
                </tbody>
            </table>

<center><h3>Vincular Persona existente</h3></center>
        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('obraPersona.search')
            </div>
           
           
        </div>



        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-dark">
                    <tr>
                       <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Apellidos</th>
                        <th scope="col" class="text-center align-middle">Cargo</th>
                        <th scope="col" class="text-center align-middle">Sector</th>
                        <th scope="col" class="text-center align-middle ">Vincular</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($personas as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->Id_persona}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">{{$item ->apellidos}}</td>
                            <td class="text-center align-middle">{{$item ->cargo}}</td>
                            <td class="text-center align-middle">
                                @if ($item ->extra==1)
                                Social
                                @elseif ($item ->extra==2)
                                Institucional/Gubernamental
                                @else
                                --------
                                @endif
                            </td>
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal-delete-{{$item->Id_persona}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/link.svg')}}" title="Vincular"></img>
                            </a>
                            </td>
                        </tr>
                    @include('obraPersona.modal')
                    @endforeach
                </tbody>
            </table>
           
            @include('obraPersona.paginador')
        </div>

@endsection