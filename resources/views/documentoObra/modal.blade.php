
<div class="modal fade" id="modal-delete-{{$item->id_obra}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'cntrl_obra','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vínculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se vinculará la Obra: </center><br>
                    <center><strong>{{$item->nombre}}</strong> </center><br> 
                    <center>con el Documento: </center><br>
                    <center><strong>({{$documento->id_doc}}){{$documento->titulo}} </strong><center>
                </p>
                <div class="form-group">
                <label for="sel1">Selecciona el Status:</label>
                    <select class="form-control" id="status" name="status">
                        @foreach($status as $item2)
                            <option value="{{$item2->id_status}}">{{$item2->tip_est}}</option>
                        @endforeach
                    </select>
                </div>
                <br><center>¿Está bien?</center>
                 <input type="hidden" name="fk_doc" class="form-control" value="{{$documento->Id_doc}}">
                 <input type="hidden" name="fk_obra" class="form-control" value="{{$item->id_obra}}">

            </div>

        
       
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>