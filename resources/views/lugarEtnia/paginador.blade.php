<!--Si hay mas de 10 elementos se mostrara el paginador-->
@if ($totalRegistros/10 > 0)
    <nav aria-label="paginador">
        <ul class="pagination justify-content-center">
            <!--Aquí se controla el bóton anterior del paginador-->
            @if ($page==1)
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$page-1}}&searchText={{$searchText}}" tabindex="-1">Anterior</a>
                    </li>
            @endif
            <?php
            //variables para controlar la vista del paginador
                $cont=0;
                $pivote=1;
                $limiteSuperior=6;
                $limiteInferior=0;
                //Se compara el número de registros entre 10 y si no es exacta la división se ajusta aumentandole 1
                //la variable totalpaginadores se utiliza para reccorrer el for; es el número de elementos que se mostrará en el paginador
                if($totalRegistros%10==0)
                {
                    $totalpaginadores=$totalRegistros/10;
                }else{
                    $totalpaginadores=intval($totalRegistros/10)+1;
                }

                //Se resta el número de la pagina actual menos el limite inferior
                //El resultado de la resta es el elemento en donde se iniciara el for solo si es mayor o igual a cero
                $auxPivote=$page-$limiteSuperior;
                //si la resta es igual o mayor a cero se cambiara el valor del pivote a la resta de auxPivote y se le agregara 1
                if($auxPivote>=0){
                    $pivote=$auxPivote+1;
                }

            ?>
             <!--Aquí se controla el inicio del paginador-->
            @if ($pivote>1)
                @if ($pivote==2)
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page=&searchText={{$searchText}}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href=""></a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page=1&searchText={{$searchText}}">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page=2&searchText={{$searchText}}">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href=""></a>
                    </li>
                @endif
            @endif
            <!--En esta parte se crean los elementos centrales del paginador-->
            @for ($i = $pivote; $i < $totalpaginadores; $i++)
                <!--Si la variable page es igual a la variable i, eso significa
                    que nos encontramos en el elemnto actual del paginador y se creará un elemnento activo-->
                @if ($page==$i)
                    <li class="page-item active">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$i}}&searchText={{$searchText}}">{{$i}} <span class="sr-only">(current)</span></a>
                    </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$i}}&searchText={{$searchText}}">{{$i}}</a>
                        </li>
                @endif
                <!--Cuando la variable cont llegue a 9 se parara el ciclo-->
                @if ($cont==7)
                    @break;
                @endif
                <?php $cont=$cont+1?>
            @endfor
            <!--Aquí se controla el final del paginador-->
            @if ($page==$totalpaginadores)
                <li class="page-item active">
                    <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$totalpaginadores}}&searchText={{$searchText}}">{{$totalpaginadores}}</a>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href=""></a>
                </li>
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$totalpaginadores}}&searchText={{$searchText}}">{{$totalpaginadores}}</a>
                    </li>
            @endif

            <!--Aquí se controla el boton siguiente del paginador-->
            @if ($page==$totalpaginadores)
                <li class="page-item disabled">
                    <a class="page-link" href="#">Siguiente</a>
                </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{url('/lugar_etnia/ligar')}}/{{$lugar->id_lugar}}?page={{$page+1}}&searchText={{$searchText}}">Siguiente</a>
                    </li>
            @endif
        </ul>
    </nav>
@endif
