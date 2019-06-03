<div class="tab-pane fade" id="obras" role="tabpanel" aria-labelledby="obras-tab">
            <br>
            <h4>Obras que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($obras)==0)
                  <center>Sin Obras vinculadas
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoObraController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>

                  @else
                  <thead>
                        <tr>
                        <th>ID</th>
                        <td>Nombre</td>
                        <td>Ver Detalle</td>
                        <td>
                              <a class="btn btn-primary" href="{{URL::action('DocumentoObraController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                        </td>      
                        </tr>
                        
                  </thead>
                  @foreach ($obras as $obra)
                  <tr>
                        <td>
                              {{$obra->id_obra}}
                        </td>
                        <td>
                              {{$obra->nombre}}
                        </td>
                         <td>
                               <div class="text-center">
                              <a href="{{URL::action('ObraController@show',$obra->id_obra)}}">
						<img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
					</a>
                               </div>
                        </td>
                        <td>
                        </td>

                  </tr>
                  @endforeach @endif

            </table>


      </div>