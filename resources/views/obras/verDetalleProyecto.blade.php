<div class="tab-pane fade" id="proyectos" role="tabpanel" aria-labelledby="proyectos-tab">
       <br>
            <table class="table table-bordered">
                  <h5>Proyectos</h5>
                  @if (count($proyectos)==0)
                  <center>Sin Proyectos asignados
                        <br>
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraProyectoController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>   
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Proyecto</th>
                        
                        <td>
                              <br>
                              <a class="btn btn-primary" href="{{URL::action('ObraProyectoController@ligarObra',$obra->id_obra)}}">Administrar</a>
                        </td>

                  </tr>
                  <tr>

                        @foreach ($proyectos as $item)
                        <td>
                              {{$item->id}}
                        </td>
                        <td>
                              {{ $item->proyecto}}
                        </td>
                        
                        <td>
                        </td>
                  </tr>
                  @endforeach @endif
            </table>  
</div>