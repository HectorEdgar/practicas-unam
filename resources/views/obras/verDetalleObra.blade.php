<div class="tab-pane fade" id="obras" role="tabpanel" aria-labelledby="obras-tab">
       <br>
            <table class="table table-bordered">
                  <h5>Obras</h5>
                  @if (count($obras)==0)
                  <center>Sin otras Obras relacionadas
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraObraController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <td>Nombre</td>
                        <td>Extra</td>
                        <td>Coordenadas</td>  
                        <td>Ver Detalle</td>                      
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('ObraObraController@ligarObra',$obra->id_obra)}}">Administrar</a>
                        </td>

                  </tr>
                  <tr>

                        @foreach ($obras as $item)
                        <td>
                              {{$item->id_obra}}
                        </td>
                        <td>
                              {{ $item->nombre}}
                        </td>
                        <td>
                               @if ($item ->extra==1) <strong style="color:darkslateblue">Obra</strong> @endif
                                @if ($item ->extra==2) <strong style="color:darkslateblue">Complejo</strong>@endif
                        </td>
                        <td>
                                 @if ($item->revisado==0) <strong style="color:firebrick">No Revisadas </strong> @endif
                                @if ($item->revisado==1) <strong style="color:green">Revisadas</strong>@endif
                            
                        </td>
                        <td>
                              <div class="text-center">
                              <a href="{{URL::action('ObraController@show',$item->id_obra)}}">
						<img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
					</a>
                               </div>
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif
            </table>  
</div>