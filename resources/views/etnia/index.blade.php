@extends('layouts.admin')
@section('titulo')
    Index de Etnia
@endsection

@section('contenido')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('EtniaController@index')}}">Etnia</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Etnias   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('etnia.search')
            </div>
            <!--Etiquetas de la imagen de agregar-->
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <a href="{{url('/etnia')}}/create" class="btn btn-outline-success col-sm-12 col-xs-12" >
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
                        <th scope="col" class="text-center align-middle">Otros Nombres</th>
                        <th scope="col" class="text-center align-middle">Familia Lingüística</th>
                        <th scope="col" class="text-center align-middle">Territorio Indígena</th>
                        <th scope="col" class="text-center align-middle ">Opciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($etnias as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->id_etnia}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">{{$item ->nombre2}}</td>
                            <td class="text-center align-middle">{{$item ->familia}}</td>                        
                            <td class="text-center align-middle">{{$item ->territorio}}</td>
                            <td class="text-center align-middle ">
                                <a href="{{URL::action('EtniaController@edit',$item ->id_etnia)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->id_etnia}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                            </td>
                        </tr>
                        @include('etnia.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$Etniaes->links()--}}
           
            @include('etnia.paginador')
        </div>

@endsection