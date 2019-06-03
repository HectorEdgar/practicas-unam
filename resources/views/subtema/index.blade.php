@extends('layouts.admin')
@section('titulo')
    Index de Subtema
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
                    <li class="breadcrumb-item"><a href="{{URL::action('SubtemaController@index')}}">Subtema</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Subtemas   
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
    </div>

        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('subtema.search')
            </div>
            
           
        </div>
        
        <br>
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">        
        
            <table class="table table-hover table-sm ">
                
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Subtema</th>
                        <th scope="col" class="text-center align-middle">Opciones</th>

                    </tr>
                </thead>
                
                <tbody>
                    @foreach($subtemas as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item ->id_sub}}</td>
                            <td class="text-center align-middle">{{$item ->subtema}}</td>
                            <td class="text-center align-middle ">
                                <a href="{{URL::action('SubtemaController@edit',$item ->id_sub)}}">
                                     <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Editar"></img>
                                </a>
                                <a href="" data-target="#modal-delete-{{$item->id_sub}}" data-toggle="modal">
                                    <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                                </a>
                                <a href="{{URL::action('SubtemaController@show',$item ->id_sub)}}">
                                    <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                            </a>
                            </td>
                        </tr>
                        @include('subtema.modal') 
                    @endforeach
                </tbody>
            </table>
            {{--$subtemaes->links()--}}
           
            @include('subtema.paginador')
        </div>

@endsection