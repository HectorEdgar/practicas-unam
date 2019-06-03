<!--Esta es la vista que crea un nuevo institucion
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Institución
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/cntrl_institucion')}}/ligar/{{$documento->Id_doc}}">Administrar Documento e Institución</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear Institución</li>
                </ol>
            </nav>
        <h3>Nueva Institución</h3>
        <h5>Este nueva institucion se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOinstitucion-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_institucion.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
    <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-info" role="alert">
                    <strong>Sector Social</strong>: Se refiere a todas aquellas organizaciones no gubernamentales u organizaciones sociales 
                    <br>
                    <strong>Sector Institucional/Gubernamental</strong>:Son todas aquellas dependencias gubernamentales, así como empresas e instituciones 
                    </div>
                </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="nombre">Nombre de Institución</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Institución..." required>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="siglas">Siglas</label>
                    <input type="text" name="siglas" class="form-control" placeholder="Siglas...">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
            <label for="pais">Elige un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="pais">País</label>
            </div>

            <input type="text" name="pais" class="form-control" >
            </div>
        </div>
            </div>
            
           
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="sector">Elige un sector:</label><br>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                            <label class="input-group-text" for="sector">Sector</label>
                    </div>
                    <select class="custom-select" name="extra" id="extra">
                        <option value="Sin Sector" selected>Sin Sector</option>
                        <option value="Sector Social">Sector Social</option>
                        <option value="Sector Institucional/Gubernamental">Sector Institucional/Gubernamental</option>
                    </select>
                </div>
        </div>
        </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-danger"  href="{{url('/cntrl_institucion')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
        </div>
    </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection