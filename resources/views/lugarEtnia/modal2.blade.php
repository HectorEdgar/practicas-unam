
<div class="modal fade" id="modal2-delete-{{$item->id_etnia}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">   
{!! Form::open(['method' => 'DELETE', 'route' => ['lugar_etnia.destroy2', $item->id_etnia,$lugar->id_lugar]]) !!}

<div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Desvinculación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <p><center>Se desvinculará la Etnia:</center> <br>
                    <center><strong>{{$item->id_etnia}} {{$item->nombre}} </strong></center> <br> 
                    <center>del Lugar:</center> <br>
                    <center><strong>({{$lugar->id_lugar}}){{$lugar->ubicacion}} </strong></center>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
            <input type="hidden" name="fk_lugar" class="form-control" value="{{$lugar->id_lugar}}">
            <input type="hidden" name="fk_etnia" class="form-control" value="{{$item->id_etnia}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Desvincular</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>