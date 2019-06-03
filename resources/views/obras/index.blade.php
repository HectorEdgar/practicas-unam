@extends('layouts.admin')
@section('titulo')
    Index de Obra
@endsection

@section('contenido')
@if(Session::has('message'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message')}}
</div>
@endif
@if(Session::has('message2'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message2')}}
</div>
@endif
@if(Session::has('message3'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('message3')}}
</div>
@endif
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('ObraController@index')}}">Obra</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Obras   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @include('obras.search')
            </div>
            <!--Etiquetas de la imagen de agregar-->
           <!-- <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <a href="{{url('/obras')}}/create" class="btn btn-outline-success col-sm-12 col-xs-12" >
                    <img width="30px" src="{{asset('imgs/agregar.svg')}}"></img>
                </a>
            </div>
             -->
           
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
                        <th scope="col" class="text-center align-middle ">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($obras as $item)
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
                            <td class="text-center align-middle ">
                                 <a href="{{URL::action('ObraController@show',$item->id_obra)}}">
							        <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
							    </a>
                                <a href="{{URL::action('ObraController@edit',$item ->id_obra)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->id_obra}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                                @if(Auth::user()->hasAnyRole(['admin','revisor'])) 
                                @if ($item->revisado==0) 
                                <a href="{{action('ObraController@validarCoordenadas',$item->id_obra)}}">
							        <img length="30px" width="30px" src="{{asset('imgs/remove-coordinates.svg')}}" title="Validar coordenadas"></img>
                                </a>
                                
                                @endif
                                @if ($item->revisado==1)
                                <a href="{{action('ObraController@validarCoordenadas',$item->id_obra)}}">
							        <img length="30px" width="30px" src="{{asset('imgs/put-coordinates.svg')}}" title="Quitar coordenadas"></img>
                                </a>
                                @endif
                                @endif


                                
                                
                            </td>
                        </tr>
                        @include('obras.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$obraes->links()--}}
            @include('obras.paginador')
        </div>

@endsection