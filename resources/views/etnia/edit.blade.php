@extends('layouts.admin') 
@section('titulo') Editar Etnia
@endsection
 
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('EtniaController@index')}}">Etnia</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h3>Editar Etnia {{$etnia->nombre}}</h3>
        @if (count($errors)>0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif 
        {!!Form::model($etnia,['method'=>'PATCH','route'=>['etnia.update',$etnia->id_etnia]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="nombre">Grupo Étnico</label>
       <input type="text" name="nombre" class="form-control" placeholder="Grupo Étnico..." value="{{$etnia->nombre}}">
        </div>
        <div class="form-group">
            <label for="nombre2">Otros Nombres</label>
            <input type="text" name="nombre2" class="form-control" placeholder="Otros Nombres.." value="{{$etnia->nombre2}}">
        </div>
        <div class="form-group">
            <label for="familia">Familia Lingüística</label>
            <input type="text" name="familia" class="form-control" placeholder="Familia Lingüística..." value="{{$etnia->familia}}">
        </div>
        <div class="form-group">
            <label for="territorio">Territorio Indígena</label>
            <input type="text" name="territorio" class="form-control" placeholder="Territorio Indígena..." value="{{$etnia->territorio}}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-secondary" href="{{ route('etnia.index') }}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection