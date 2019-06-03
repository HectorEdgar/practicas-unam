
<div class="modal fade" id="modal-delete-{{$item->id_lugar}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
{!!Form::open(array('url'=>'obra_lugar','method'=>'POST','autocomplete'=>'off'))!!} {{Form::token()}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de Vínculo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="borrar()">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-sm-12">
                <p>Se vinculará el Lugar:
                    <strong>({{$item->id_lugar}}) {{$item->ubicacion}}</strong><br>
                    con la Obra:
                    <strong>({{$obra->id_obra}}){{$obra->nombre}} </strong>
                    <p>Selecciona las coordenadas:</p>
                    Latitud:  <input type="text" style="width:400px" id="latitud" />
                    
                    Longitud: <input type="text" style="width:400px" id="longitud" />
                </div>
                    <br>
                    <div class="col-md-12 col-sm-12" id="map-canvas" style="height:200px;"></div>
                    <br><center>¿Está bien?</center>
                </p>
            </div>
        <input type="hidden" name="fk_obra" class="form-control" value="{{$obra->id_obra}}">
        <input type="hidden" name="fk_lugar" class="form-control" value="{{$item->id_lugar}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
    {{Form::Close()}}
</div>


<script>
   var map;
   function initialize() {
   map = new google.maps.Map(document.getElementById('map-canvas'), {
     zoom: 3,
     center: {lat: -10, lng: -60}
   });
   
   var marker=new google.maps.Marker({
      position:map.getCenter(), 
      map:map, 
      draggable:true
   });
      google.maps.event.addListener(marker,'dragend',function(event) {
      document.getElementById("latitud").value = this.getPosition().lat().toString();
      document.getElementById("longitud").value = this.getPosition().lng().toString();
   });
}
google.maps.event.addDomListener(window, 'load', initialize);




</script>