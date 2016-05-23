$(document).ready(function() {
    function crearAjax(){
        /* Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error */
        var objetoAjax = false;
        //Preguntando si nuestro querido usuario aún usa Internet Explorer
        if(navigator.appName=="Microsoft Internet Explorer")
            objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
        else //De lo contrario está usando otro navegador, por supuesto uno mejor
            objetoAjax = new XMLHttpRequest();
        return(objetoAjax); //Retornamos nuestro objeto
    };

    $('#formEstCivil').submit(function(evento) {
        evento.preventDefault();
        var $objXhr = crearAjax(); // intentamos crear el objeto ajax
        if ($objXhr === false) {
            $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al crear el objeto ajax. <br> Contacte a Informática!.");
            return false;
        }

        $objXhr = $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            beforeSend:function(xhr) {
                $("#isi_msjProcesando").removeClass('isi_ocultar');
            },
            success:function(data, otro, otromas) {
                // isi_msj_popUp.MaterialSnackbar.showSnackbar({
                //     message: "Se agregó '" + $("#est_civiles_descrip").val().toUpperCase() + "' INDEC: " + $("#est_civiles_codindec").val()
                //     , timeout: 2500
                // setTimeout(function() { window.location.reload() }, 2000);
                // });
                window.location.reload(true);
            },
            error:function(xhr, textStatus, errorThrown) {
                isi_msj_popUp.MaterialSnackbar.showSnackbar({
                    message: "Ups!: " + errorThrown.toUpperCase()
                    , timeout: 2500
                });
                // $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar agregar (" + errorThrown + ")");
                $("#isi_msjProcesando").addClass('isi_ocultar');
                return false;
            }
        });
    });

    $("#isi_lnk_verAllChk").click(function(evento) {
        if ($(this).html()=="Ocultar MultiCheck")
            $(this).html("Mostrar MultiCheck");
        else
            $(this).html("Ocultar MultiCheck");
    });

    // focos a objetos
    $("#isi_lnk_addEstCivil").click(function(evento) {
        $("#est_civiles_descrip").focus();
    });

    $("#isi_lnk_verBusc").click(function(evento) {
        $("#isi_inpTxt_buscar").focus();
    });

});
