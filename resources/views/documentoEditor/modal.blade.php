
<div class="modal fade" id="modal-delete-{{$item->id_editor}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'cntrl_editor','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vínculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se vinculará el Editor: </center><br>
                    <center><strong>{{$item->editor}} </strong> </center><br> 
                    <center>con el Documento: </center><br>
                    <center><strong>({{$documento->Id_doc}}){{$documento->titulo}} </strong><center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
        <input type="hidden" name="fk_doc" class="form-control" value="{{$documento->Id_doc}}">
        <input type="hidden" name="fk_editor" class="form-control" value="{{$item->id_editor}}">
        <input type="hidden" name="orden" class="form-control" value="1">
        <input type="hidden" name="extra" class="form-control" value="1">
        

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>