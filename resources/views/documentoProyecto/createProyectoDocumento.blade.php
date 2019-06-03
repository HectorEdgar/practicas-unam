<!--Esta es la vista que crea un nuevo proyecto
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Proyecto
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/cntrl_proyecto')}}/ligar/{{$documento->Id_doc}}">Administrar Documento y Proyecto</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Proyecto</li>
                </ol>
        </nav>
        <h3>Nuevo Proyecto</h3>
        <h5>Este nuevo proyecto se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOproyecto-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_proyecto.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="proyecto">Proyecto</label>
            <input type="text" name="proyecto" class="form-control" placeholder="Proyecto...">
        </div>
       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-danger" href="{{url('/cntrl_proyecto')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
            </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection