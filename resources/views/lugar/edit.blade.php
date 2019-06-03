@extends('layouts.admin') 
@section('titulo') Editar lugar
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('LugarController@index')}}">Lugar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Lugar {{$lugar->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($lugar,['method'=>'PATCH','route'=>['lugar.update',$lugar->id_lugar]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="ubicacion">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" placeholder="Ubicación..." value="{{$lugar->ubicacion}}">
        </div>
          <div class="form-group">
            <label for="pais">Elige un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
            <label class="input-group-text" for="pais">País</label>
            </div>

                <select class="custom-select" name="pais" id="pais">
                    <option value="0" selected>{{$lugarEdit->pais}}</option>
                    @foreach($paises as $item)
                    <option value="{{$item->id_pais}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>

           <div class="form-group">
            <label for="sector">Eliga una Región Geográfica:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="region">Región</label>
            </div>
                <select class="custom-select" name="region_geografica" id="region_geografica">
                <option value="0" selected>{{$lugarEdit->region}}</option></option>
                    @foreach($regiones as $item)
                    <option value="{{$item->id_region}}">{{$item->nombrereg}}</option>
                    @endforeach
                </select>
            </div>
            </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('lugar.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection