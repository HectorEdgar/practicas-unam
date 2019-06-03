@extends('layouts.admin')
@section('titulo')
    Index de descarga
@endsection

@section('contenido')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Descargas</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Descargas   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('descarga.search')
            </div>
            <!--Etiquetas de la imagen de agregar-->
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <a href="{{URL::action('DescargaController@create')}}" class="btn btn-outline-success col-sm-12 col-xs-12" >
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
                        <th scope="col" class="text-center align-middle">Título</th>
                        <th scope="col" class="text-center align-middle">Url</th>
                        <th scope="col" class="text-center align-middle">Fecha Ingreso</th>
                        <th scope="col" class="text-center align-middle">Estado</th>
                        <th scope="col" class="text-center align-middle">Proyecto</th>
                        <th scope="col" class="text-center align-middle">Archivo</th>
                        <th scope="col" class="text-center align-middle ">Opciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($descargas as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->idDescarga}}</td>
                            <td class="text-center align-middle">{{$item ->titulo}}</td>
                            <td class="text-center align-middle">{{$item ->url}}</td>
                            <td class="text-center align-middle">{{$item ->fechaIngreso}}</td>
                            <td class="text-center align-middle">{{$item ->estado}}</td>
                            <td class="text-center align-middle">{{$item ->tipoProyecto}}</td>
                            <td class="text-center align-middle"><a href="{{URL::action('DescargaController@download',$item ->archivo->nombre)}}">{{$item ->archivo->nombre}}</a></td>
                            <td class="text-center align-middle ">
                                <a href="{{URL::action('DescargaController@edit',$item ->idDescarga)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->idDescarga}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                            </td>
                        </tr>
                        @include('descarga.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$descargas->links()--}}
           
            @include('descarga.paginador')
        </div>

@endsection