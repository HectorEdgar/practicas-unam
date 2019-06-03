$(document).ready(function() {
    $("#inFechaExtra").hide();

    $(".fechaNormal").click(function(evento) {

        var valor = $(this).val();

        if (valor == 1) {
            $("#inFechaNormal").show();
            $("#inFechaExtra").hide();
            $("#fechaExtraAño").prop('required', false);

        } else {
            $("#inFechaNormal").hide();
            $("#inFechaExtra").show();
            $("#fechaExtraAño").prop('required', true);
        }
    });


    /// muestra los divs segun tipo de documento




    $('#Tipo').on('change', function(e) {
        //var optionSelected = $("option:selected", this);
        var valorSeleccionado = parseInt(this.value);


        switch (valorSeleccionado) {
            case 2:
                $('.tipoDocumento').attr("hidden", true);
                $("#boletin").attr("hidden", false);
                break;
            case 8:
                $('.tipoDocumento').attr("hidden", true);
                $("#libro").attr("hidden", false);
                break;
            case 10:
                $('.tipoDocumento').attr("hidden", true);
                $("#ponencia").attr("hidden", false);
                break;
            case 13:

                $('.tipoDocumento').attr("hidden", true);
                $("#tesis").attr("hidden", false);
                break;
            case 14:
                $('.tipoDocumento').attr("hidden", true);
                $("#artRevista").attr("hidden", false);
                break;
            case 15:
                $('.tipoDocumento').attr("hidden", true);
                $("#capLibro").attr("hidden", false);
                break;
            case 17:
                $('.tipoDocumento').attr("hidden", true);
                $("#revista").attr("hidden", false);
                break;
            case 18:
                $('.tipoDocumento').attr("hidden", true);
                $("#artBoletin").attr("hidden", false);
                break;
            default:
                $('.tipoDocumento').attr("hidden", true);
                break;
        }

    });



});