@extends('layouts.admin')
@section('titulo')
    Index de autor
@endsection

@section('contenido')
@if(Session::has('exito'))
<div class="alert alert-success">
  <strong>¡Éxito!</strong> {{Session::get('exito')}}
</div>
@endif
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('AutorController@index')}}">Autor</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Autores   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('autor.search')
            </div>
           
           
        </div>
        
        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Pseudónimo</th>
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Apellidos</th>
                        <th scope="col" class="text-center align-middle ">Opciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($autores as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->Id_autor}}</td>
                            <td class="text-center align-middle">{{$item ->pseudonimo}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">{{$item ->apellidos}}</td>
                            <td class="text-center align-middle ">
                                <a href="{{URL::action('AutorController@edit',$item ->Id_autor)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->Id_autor}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                                <a href="{{URL::action('AutorController@show',$item ->Id_autor)}}">
                                        <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                 </a>
                            </td>
                        </tr>
                        @include('autor.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$autores->links()--}}
           
            @include('autor.paginador')
        </div>

@endsection