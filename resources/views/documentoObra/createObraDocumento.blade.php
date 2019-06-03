<!--Esta es la vista que crea un nuevo obra
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Obra
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                <li class="breadcrumb-item"><a href="{{url('/cntrl_obra')}}/ligar/{{$documento->Id_doc}}">Administrar Documento y Obra</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Obra</li>
                </ol>
            </nav>
        <h3>Nueva Obra</h3>
        <h5>Este nueva obra se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOobra-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_obra.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
        <div class="form-group">
            <label for="nombre">Nombre de la Infraestructura:</label>
            <input type="text" name="nombre" class="form-control" placeholder="Nombre...">
        </div>

         <div class="form-group">
            <div class="alert alert-info" role="alert">
                <strong>Especifíque:</strong> Esta Infraestructura se trata de:
                <select class="custom-select" name="tipo" id="tipo">
                    <option value="obra">Obra</option>
                    <option value="complejo">Complejo</option>
                </select>
            </div>
         </div>
        <div class="form-group">
                <label for="sel1">Selecciona el Status:</label>
                    <select class="form-control" id="status" name="status">
                        @foreach($status as $item2)
                            <option value="{{$item2->id_status}}">{{$item2->tip_est}}</option>
                        @endforeach
                    </select>
        </div>
       <div class="form-group">
            	<button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-danger" href="{{url('/cntrl_obra')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
            </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection