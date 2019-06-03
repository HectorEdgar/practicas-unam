<!--Esta es la vista que crea un nuevo autor
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Autor
@endsection
@section('contenido')
@if(Session::has('Aviso'))
<div class="alert alert-warning">
  <strong>¡Éxito!</strong> {{Session::get('Aviso')}}
</div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/cntrl_autor')}}/ligar/{{$documento->Id_doc}}">Administrar Documento y Autor</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Autor</li>
                </ol>
            </nav>
        <h3>Nuevo Autor</h3>
        <h5>Este nuevo autor se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOAUTOR-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_autor.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="pseudonimo">Pseudónimo</label>
            <input type="text" name="pseudonimo" class="form-control" placeholder="Pseudónimo...">
        </div>
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre...">
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos...">
        </div>

        <div class="form-group">
        <label for="apellidos">Tipo <strong>(extra)</strong></label>
        <select name="extra" class="form-control">
                <option value=" " selected>- Seleccione - </option>
                <option value="ed">Editor</option>
                <option value="coord">Coordinador</option>
                <option value="comp">Compilador</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-danger" href="{{url('/cntrl_autor')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
        </div>

        {!!Form::close()!!}
    </div>
</div>
@endsection