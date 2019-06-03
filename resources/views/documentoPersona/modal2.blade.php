
<div class="modal fade" id="modal2-delete-{{$item->Id_persona}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">   
{!! Form::open(['method' => 'DELETE', 'route' => ['cntrl_persona.destroy2', $item->Id_persona,$documento->Id_doc]]) !!}

<div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Desvinculación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se desvinculará la Persona:</center> <br>
                    <center><strong>{{$item->fk_persona}} </strong></center> <br> 
                    <center>del Documento:</center> <br>
                    <center><strong>({{$documento->Id_doc}}){{$documento->titulo}} </strong></center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
            <input type="hidden" name="fk_doc" class="form-control" value="{{$documento->Id_doc}}">
            <input type="hidden" name="fk_persona" class="form-control" value="{{$item->Id_persona}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Desvincular</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>