
<div class="modal fade" id="modal2-delete-{{$item->orden}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    {{Form::Open(array('action'=>array('DocumentoAutorController@destroy',$item->orden),'method'=>'delete'))}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Desvinculación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se desvinculará el Autor:</center> <br>
                    <center><strong>{{$item->fk_autor}} </strong></center> <br> 
                    <center>del Documento:</center> <br>
                    <center><strong>({{$documento->Id_doc}}){{$documento->titulo}} </strong></center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
       
        

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Desvincular</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>