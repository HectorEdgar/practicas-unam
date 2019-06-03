
<div class="modal fade" id="modal-delete-{{$item->Id_persona}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'obra_persona','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vínculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se vinculará el Persona: </center><br>
                    <center><strong>{{$item->Id_persona}} {{$item->nombre}}{{$item->apellidos}} </strong> </center><br> 
                    <center>con la Obra: </center><br>
                    <center><strong>({{$obra->id_obra}}){{$obra->nombre}} </strong><center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
        <input type="hidden" name="fk_obra" class="form-control" value="{{$obra->id_obra}}">
        <input type="hidden" name="fk_persona" class="form-control" value="{{$item->Id_persona}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>