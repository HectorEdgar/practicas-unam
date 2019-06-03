$(document).ready(function() {

    var valorSeleccionado = parseInt('<?php echo $documento->tipo;?>');



    switch (valorSeleccionado) {
        case 2:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaRevista_boletin").attr("hidden", false);
            break;
        case 8:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaLibro").attr("hidden", false);
            break;
        case 10:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaPonencias").attr("hidden", false);
            break;
        case 13:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaTesis").attr("hidden", false);
            break;
        case 14:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaRevista_boletin").attr("hidden", false);
            break;
        case 15:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaCapLibro").attr("hidden", false);
            break;
        case 17:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaRevista_boletin").attr("hidden", false);
            break;
        case 18:
            $('.tipoDocumento').attr("hidden", true);
            $("#tablaRevista_boletin").attr("hidden", false);
            break;

        default:
            $('.tipoDocumento').attr("hidden", true);
            break;
    }








});