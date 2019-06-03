
<div class="modal fade" id="modal3-delete-{{$item->Id_institucion}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">   
{!! Form::open(['method' => 'DELETE', 'route' => ['obra_institucion.destroy3', $item->Id_institucion,$obra->id_obra,]]) !!}

<div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Cambio de Relación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Modificar Relación de la Institución:</center> <br>
                    <center><strong>{{$item->Id_institucion}} {{$item->nombre}} </strong></center> <br> 
                    <center>con la Obra:</center> <br>
                    <center><strong>({{$obra->id_obra}}){{$obra->nombre}} </strong></center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success">Cambiar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>