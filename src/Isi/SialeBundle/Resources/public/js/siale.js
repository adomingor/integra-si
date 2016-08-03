// boton submit del form ftips
$('#btnConsultar').on('click', function (evento) {
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
    objXhr = $.ajax({
        url: this.href,
        method: "POST",
        headers: {
                'Content-Type':'text/csv',
                'Content-Type':'data:application/csv;charset=utf-8',
                'Content-Disposition':'attachment; filename=sialeHeader.csv'
            },
        beforeSend:function(xhr){
            $("#isi_msjProcesando").removeClass("isi_ocultar");
        },
        success:function(response, status, request){
            $("#isi_msjProcesando").removeClass("isi_ocultar");
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
            $("#isi_msjProcesando").addClass("isi_ocultar");
        },
        error:function(xhr, textStatus, errorThrown){
            $("#isi_msjPag").html("<div class='alert alert-danger text-center'><i class='fa fa-bug' aria-hidden='true'></i><strong> Ups!</strong> algo ocurrió al intentar generar el informe</div>");
        }
    });
    return objXhr;
});
