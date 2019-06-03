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
  <strong>¡Aviso! </strong> {{Session::get('flash_message2')}}
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
                    <li class="breadcrumb-item"><a href="{{url('/obras')}}">Obras</a></li>
                <li class="breadcrumb-item"><a href="{{url('/obras')}}/{{$obra->id_obra}}">Resumen Obra ({{ $obra->id_obra}})</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Administrar Obra y Lugar</li>
                </ol>
            </nav>
            <h3 class="text-center">
                Listado de Lugares vinculados<br>

                    ID de la obra : ( {{$obra->id_obra}}  )
            </h3>
            <div class="clearfix"><br></div><div class="clearfix"></div>
        </div>
    </div>

    <!-- Tabla de lugars agregados -->
        <div class="table-responsive table-responsive-xl table-responsive-md table-responsive-lg table-responsive-sm">
            <table class="table table-hover table-sm ">
                <thead class="thead-light" style="background-color: green">
                    <tr>
                       <th scope="col" class="text-center align-middle">Id</th>
                        <th scope="col" class="text-center align-middle">Ubicación</th>
                        <th scope="col" class="text-center align-middle">País</th>
                        <th scope="col" class="text-center align-middle">Región Geográfica</th>
                        <th scope="col" class="text-center align-middle">Latitud</th>
                        <th scope="col" class="text-center align-middle">Longitud</th>
                        <th scope="col" class="text-center align-middle">Complejo</th>
                        <th scope="col" class="text-center align-middle ">Opciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($lugaresdelaObra as $item)
                        <tr>
                           <th scope="row"  class="text-center align-middle">{{$item->id_lugar}}</td>
                            <td class="text-center align-middle">{{$item->ubicacion}}</td>
                            <td class="text-center align-middle">{{$item->pais}}</td>
                            <td class="text-center align-middle">{{$item->region}}</td>
                            <td class="text-center align-middle">{{$item->latitud}}</td>
                            <td class="text-center align-middle">{{$item->longitud}}</td>
                            <td class="text-center align-middle">
                            @if($item->complejo==null)
                              <p><strong>- - - - - </strong></p>
                              @else
                             <strong>({{$item->complejo}})</strong><br>
                             @endif
                            <td class="text-center align-middle ">
                            <a href="" data-target="#modal2-delete-{{$item->id_lugar}}" data-toggle="modal">
                            <img length="30px" width="30px" src="{{asset('imgs/eliminar.svg')}}" title="Eliminar"></img>
                            </a>
                            <a href="{{url('/obra_lugar')}}/editar/{{$obra->id_obra}}/{{$item->id_lugar}}">
                            <img length="30px" width="30px" src="{{asset('imgs/editar.svg')}}" title="Vincular"></img>
                            </a>
                            </td>
                        </tr>
                    @include('obraLugar.modal2')
                    @endforeach
                </tbody>
            </table>

<center><h3>Vincular lugar existente</h3></center>
        <!--Etiquetas del formulario de busqueda-->
       <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                @include('obraLugar.search')
            </div>

             <!--Etiquetas de la imagen de agregar-->
           <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                   <a href="{{URL::action('ObraLugarController@nuevoObraLugar',$obra->id_obra)}}" class="btn btn-outline-success col-sm-12 col-xs-12" >
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
                        <th scope="col" class="text-center align-middle">Pais</th>
                        <th scope="col" class="text-center align-middle">Región Geográfica</th>
                        <th scope="col" class="text-center align-middle ">Vincular</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($lugares as $item)
                        <tr>
                            <th scope="row"  class="text-center align-middle">{{$item->id_lugar}}</td>
                            <td class="text-center align-middle">{{$item->ubicacion}}</td>
                            <td class="text-center align-middle">{{$item->pais}}</td>
                            <td class="text-center align-middle">{{$item->region_geografica}}</td>
                            <td class="text-center align-middle ">

                            <a href="{{url('/obra_lugar/vincular/')}}/{{$obra->id_obra}}/{{$item->id_lugar}}">
                            <img length="30px" width="30px" src="{{asset('imgs/link.svg')}}" title="Vincular"></img>
                            </a>
                            </td>
                        </tr>
                    @include('obraLugar.modal')
                    @endforeach
                </tbody>
            </table>

            @include('obraLugar.paginador')
        </div>

@endsection