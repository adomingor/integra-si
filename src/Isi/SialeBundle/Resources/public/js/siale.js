// boton submit del form ftips
$('#btnConsultar').on('click', function (evento) {
    // VER ANTES QUE ESTE TILDADO EL checkbox
    if( $('#form_chkConfirma').is(':checked') ) {
        $(this).attr('disabled','disabled');
        $("#resuCons").html("");
        $("#isi_msjProcesando").removeClass("inf_ocultar");
    }
});

// formulario de toda informacion posible del siale
$("#fLegMotOrigPers" ).submit(function( event ) {
    $("#form_chkConfirma").attr('checked', false); // una vez enviado el formulario destilda el check obligatorio para hacer la consulta
});

$('#lnkDescargCSV').click(function(e) {
    $('#lnkDescargCSV').fadeOut(1000);
    e.preventDefault();
    var $desde = $('#form_fDde').val();
    var $hasta = $('#form_fHta').val();
    objXhr = $.ajax({
        url:'conslegmotorigperscsv/' + $desde + '/' + $hasta,
        method:'POST',
        headers: {
                'Content-Type':'text/csv',
                'Content-Type':'data:application/csv;charset=utf-8',
                'Content-Disposition':'attachment; filename=sialeHeader.csv'
            },
        beforeSend:function(xhr){
            $("#isi_msjProcesando").removeClass("inf_ocultar");
            // $("#mostrarProgreso").html("<div class='well text-center'>Preparando el informe &nbsp;&nbsp; <i class='fa fa-spinner fa-pulse'></i></div>");
        },
        success:function(response, status, request){
            $("#isi_msjProcesando").removeClass("inf_ocultar");
            // $("#mostrarProgreso").html("<div class='text-info text-center'><h1><span class='glyphicon glyphicon-ok'></span></h1></div>");
            // var blob = new Blob([response], {type: 'text/csv: charset=UTF-8'});
            // window.open(URL.createObjectURL(blob));
            // blob = null;

            // var $csv = JSON2CSV(response, '¬', true, true); // este es cuando envio respuesta JSON desde el controlador y llamo al metodo de abajo
            var $csv = response;
            var $downloadLink = document.createElement("a");
            var $blob = new Blob(["\ufeff", $csv], {type: 'text/csv: charset=UTF-8'});
            var $url = URL.createObjectURL($blob);
            $downloadLink.href = $url;
            $downloadLink.download = "siale.csv";

            document.body.appendChild($downloadLink);
            $downloadLink.click();
            document.body.removeChild($downloadLink);
            $csv = $blob = $downloadLink = $url = null;
        },
        error:function(xhr, textStatus, errorThrown){
            // $("#mostrarProgreso").html("<div class='alert alert-danger text-center'><i class='fa fa-bug'></i><strong> Ups!</strong> algo ocurrió al intentar generar el informe</div>");
            $("#isi_msjPag").html("<div class='alert alert-danger text-center'><i class='fa fa-bug' aria-hidden='true'></i><strong> jajaja Ups!</strong> algo ocurrió al intentar generar el informe</div>");
        }
    });
    return objXhr;
});

function JSON2CSV(objArray, separador, nombreColumnas, comillaDatos) {
    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
    var str = '';
    var line = '';

    if (nombreColumnas) {
        var head = array[0];
        if (comillaDatos) {
            for (var index in array[0]) {
                var value = index + "";
                line += '"' + value.replace(/"/g, '""') + '"' + separador;
            }
        } else {
            for (var index in array[0]) {
                line += index + separador;
            }
        }

        line = line.slice(0, -1);
        str += line + '\r\n';
    }

    for (var i = 0; i < array.length; i++) {
        var line = '';

        if (comillaDatos) {
            for (var index in array[i]) {
                var value = array[i][index] + "";
                line += '"' + value.replace(/"/g, '""') + '"' + separador;
            }
        } else {
            for (var index in array[i]) {
                line += array[i][index] + separador;
            }
        }

        line = line.slice(0, -1);
        str += line + '\r\n';
    }
    return str;
}
