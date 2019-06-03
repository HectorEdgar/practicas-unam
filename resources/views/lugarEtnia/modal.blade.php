
<div class="modal fade" id="modal-delete-{{$item->id_etnia}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'lugar_etnia','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vinculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="borrar()">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                <p><center>Se vinculará la Etnia: </center><br>
                    <center><strong>{{$item->nombre}} ({{$item->familia}}) </strong> </center><br> 
                    <center>con el Lugar: </center><br>
                    <center><strong>({{$lugar->id_lugar}}) {{$lugar->ubicacion}} </strong><center>
                    <br><center>¿Está bien?</center>
                </p>
                </div>
                
            </div>
        <input type="hidden" name="fk_lugar" class="form-control" value="{{$lugar->id_lugar}}">
        <input type="hidden" name="fk_etnia" class="form-control" value="{{$item->id_etnia}}">
        <input type="hidden" name="latitud" class="form-control" value=" ">
        <input type="hidden" name="longitud" class="form-control" value=" ">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>
