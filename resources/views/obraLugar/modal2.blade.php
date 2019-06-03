
<div class="modal fade" id="modal2-delete-{{$item->id_lugar}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">   
{!! Form::open(['method' => 'DELETE', 'route' => ['obra_lugar.destroy2', $item->id_lugar,$obra->id_obra]]) !!}

<div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Desvinculación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se desvinculará el Lugar:</center> <br>
                    <center><strong>{{$item->id_lugar}} {{$item->ubicacion}} </strong></center> <br> 
                    <center>de la Obra:</center> <br>
                    <center><strong>({{$obra->id_obra}}){{$obra->nombre}} </strong></center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
            <input type="hidden" name="fk_obra" class="form-control" value="{{$obra->id_obra}}">
            <input type="hidden" name="fk_lugar" class="form-control" value="{{$item->id_lugar}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Desvincular</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>