@extends('layouts.admin')
@section('titulo')
    Index de Persona
@endsection

@section('contenido')
@if(Session::has('message'))
<div class="alert alert-success">
  <strong>¡Éxito! </strong> {{Session::get('message')}}
</div>
@endif
@if(Session::has('message2'))
<div class="alert alert-success">
  <strong>¡Éxito! </strong> {{Session::get('message2')}}
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
                    <li class="breadcrumb-item"><a href="{{url('/paises')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('PersonaController@index')}}">Persona</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Personas   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('persona.search')
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
                        <th scope="col" class="text-center align-middle ">Opciones</th>
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
                                <a href="{{URL::action('PersonaController@edit',$item ->Id_persona)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->Id_persona}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                                <a href="{{URL::action('PersonaController@show',$item ->Id_persona)}}">
                                    <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                    </a>
                            </td>
                        </tr>
                        @include('persona.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$personaes->links()--}}
           
            @include('persona.paginador')
        </div>

@endsection