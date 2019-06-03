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
  <strong>¡Aviso!</strong> {{Session::get('flash_message2')}}
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
                    <li class="breadcrumb-item active" aria-current="page">Administrar Documento y Obra</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Obras vinculadas<br> 
                 
                    Id del documento : ( {{$documento->Id_doc}}  )
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
    </div>

    <!-- Tabla de obras agregados -->
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-light" style="background-color: green">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Extra</th>
                        <th scope="col" class="text-center align-middle">Coordenadas</th>
                        <th scope="col" class="text-center align-middle">Status</th>
                        <th scope="col" class="text-center align-middle ">Eliminar</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($obrasdelDocumento as $item)
                        <tr>
                           <th scope="row"  class="text-center align-middle">{{$item ->id_obra}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">
                                 @if ($item ->extra==1) <strong style="color:darkslateblue">Obra</strong> @endif
                                @if ($item ->extra==2) <strong style="color:darkslateblue">Complejo</strong>@endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($item->revisado==0) <strong style="color:firebrick">No Revisadas </strong> @endif
                                @if ($item->revisado==1) <strong style="color:green">Revisadas</strong>@endif
                            
                            </td>
                             <td class="text-center align-middle">
                                 @if ($item->status=='Ninguno')
                                <strong style="color:red">{{$item->status}}</strong>
                               @else
                                <strong style="color:rebeccapurple">{{$item->status}}</strong>
                               @endif
                            </td>
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal2-delete-{{$item->id_obra}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                            </a>

                            <a href="" data-target="#modalEdit-{{$item->id_obra}}" data-toggle="modal">
                                <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                            </td>
                        </tr>
                    @include('documentoObra.modal2')
                    @include('documentoObra.modalEdit')
                    @endforeach
                </tbody>
            </table>

<center><h3>Vincular Obra existente</h3></center>
        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('documentoObra.search')
            </div>
            <!--Etiquetas de la imagen de agregar-->
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <a href="{{URL::action('DocumentoObraController@nuevoObraDocumento',$documento->Id_doc)}}" class="btn btn-outline-success col-sm-12 col-xs-12" >
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
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Extra</th>
                        <th scope="col" class="text-center align-middle">Coordenadas</th>
                        <th scope="col" class="text-center align-middle ">Vincular</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($obras as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->id_obra}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">
                                 @if ($item->extra==1) <strong style="color:darkslateblue">Obra</strong> @endif
                                @if ($item->extra==2) <strong style="color:darkslateblue">Complejo</strong>@endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($item->revisado==0) <strong style="color:firebrick">No Revisadas </strong> @endif
                                @if ($item->revisado==1) <strong style="color:green">Revisadas</strong>@endif
                            </td>
                            
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal-delete-{{$item->id_obra}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/link.svg')}}" title="Vincular"></img>
                            </a>
                            </td>
                        </tr>
                    @include('documentoObra.modal')
                    
                    @endforeach
                </tbody>
            </table>
           
            @include('documentoObra.paginador')
        </div>

@endsection