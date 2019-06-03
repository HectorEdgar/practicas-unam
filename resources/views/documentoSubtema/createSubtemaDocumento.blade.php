<!--Esta es la vista que crea un nuevo subtema
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Subtema
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/cntrl_subtema')}}/ligar/{{$documento->Id_doc}}">Administrar Documento y Subtema</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear Subtema</li>
                </ol>
            </nav>
        <h3>Nueva Subtema</h3>
        <h5>Este nuevo Subtema se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOsubtema-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_subtema.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="subtema">Subtema</label>
            <input type="text" name="subtema" class="form-control" placeholder="Subtema...">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-danger" href="{{url('/cntrl_subtema')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection