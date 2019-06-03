@extends('layouts.admin')
@section('titulo')
    Index de Institución
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
  <strong>¡Éxito! </strong> {{Session::get('message3')}}
</div>
@endif
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{URL::action('InstitucionController@index')}}">Institución</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Instituciones
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>

        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('institucion.search')
            </div>
            

        </div>

        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">

            <table class="table table-hover table-sm ">

                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Nombre</th>
                        <th scope="col" class="text-center align-middle">Siglas</th>
                        <th scope="col" class="text-center align-middle">País</th>
                        <th scope="col" class="text-center align-middle ">Sector</th>
                        <th scope="col" class="text-center align-middle ">Opciones</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach($instituciones as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->Id_institucion}}</td>
                            <td class="text-center align-middle">{{$item ->nombre}}</td>
                            <td class="text-center align-middle">{{$item ->siglas}}</td>
                            <td class="text-center align-middle">{{$item ->pais}}</td>
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
                                <a href="{{URL::action('InstitucionController@edit',$item ->Id_institucion)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->Id_institucion}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                                <a href="{{URL::action('InstitucionController@show',$item ->Id_institucion)}}">
                                        <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                </a>
                            </td>
                        </tr>
                        @include('institucion.modal')
                    @endforeach
                </tbody>
            </table>
            {{--$instituciones->links()--}}

            @include('institucion.paginador')
        </div>

@endsection