
<div class="modal fade" id="modal-delete-{{$item->Id_autor}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'cntrl_autor','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vínculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se vinculará el Autor: </center><br>
                    <center><strong>{{$item->nombre}} </strong> </center><br> 
                    <center>con el Documento: </center><br>
                    <center><strong>({{$documento->Id_doc}}){{$documento->titulo}} </strong></center>
                    <br><br>
                    <center>
                            <div class="form-group">
                                    <label for="extra">Tipo <strong>(Extra) </strong></label>
                        
                                    <select name="extra" class="form-control">
                                        <option value=" " selected>- Seleccione - </option>
                                        <option value="ed">Editor</option>
                                        <option value="coord">Coordinador</option>
                                        <option value="comp">Compilador</option>

                                    </select>
                                </div>
                    </center>
                                
                    <br><center>¿Está bien?</center>
                </p>
            </div>
        <input type="hidden" name="fk_doc" class="form-control" value="{{$documento->Id_doc}}">
        <input type="hidden" name="fk_autor" class="form-control" value="{{$item->Id_autor}}">        

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>