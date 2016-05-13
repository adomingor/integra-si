$(document).ready(function(){

    $("#isi_lnk_busqEC").click(function(evento) {
        $("#isi_div_busqEstCivil").toggleClass("isi_quitarElemento");
        if ($("#isi_div_busqEstCivil").hasClass("isi_quitarElemento"))
            $("#isi_lnk_busqEC").html("Activar buscador");
        else
            $("#isi_lnk_busqEC").html("Ocultar buscador");

    });

    function tildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (index, element) {
            element.MaterialCheckbox.check();
        });
    }

    function desTildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (index, element) {
            element.MaterialCheckbox.uncheck();
        });
    }

    $("#isi_checkTodos").click(function(evento) {
        if ($("#isi_checkTodos").length) { // si existe en la pagina el checkbox de (des)Tildar todos
            if (this.checked)
                tildarMultiCheck("isi_chkMultiAccion");
            else
                desTildarMultiCheck("isi_chkMultiAccion"); // destildo resto multicheck
        }
    });

    function alternarChkTodos() {
        desTildarMultiCheck("isi_checkTodos"); // destildo cabecera
        desTildarMultiCheck("isi_chkMultiAccion"); // destildo resto multicheck
        $("[name='isi_verSiNo']").toggleClass("isi_quitarElemento");
        if ($("[name='isi_verSiNo']").hasClass("isi_quitarElemento"))
            $("#isi_verChekTodos").html("Mostrar MultiCheck");
        else
            $("#isi_verChekTodos").html("Ocultar MultiCheck");
    }

    $("#isi_verChekTodos").click(function(evento) {
        alternarChkTodos();
    });

    function crearAjax(){
        /* Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error */
        var objetoAjax = false;
        //Preguntando si nuestro querido usuario aún usa Internet Explorer
        if(navigator.appName=="Microsoft Internet Explorer")
            objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
        else //De lo contrario está usando otro navegador, por supuesto uno mejor
            objetoAjax = new XMLHttpRequest();
        return(objetoAjax); //Retornamos nuestro objeto
    }

    /* Elimina los registros marcados con el check en una (ver listado de estado civil)  */
    $("#isi_lnk_borrarRegs").click(function(evento) {
        evento.preventDefault();
        var notification = document.querySelector('.mdl-js-snackbar');
        var $cantChks = $("input[name='isi_chkMultiAccion']:checked").length;
        var $totRegi = $("#tituTLista span.mdl-badge").attr("data-badge"); //null = undefined = no hay badge

        // verificamos que este visible la columna de selección múltiple
        if ($("[name='isi_verSiNo']").hasClass("isi_quitarElemento")) {
            notification.MaterialSnackbar.showSnackbar({
                message: "Active la opción 'MultiCheck'"
                , timeout: 2500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
            return false;
        }

        var $objXhr = crearAjax(); // intentamos crear el objeto ajax
        if ($objXhr === false) {
            $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al crear el objeto ajax. <br> Contacte a Informática!.");
            return false;
        }

        $("input[name='isi_chkMultiAccion']:checked").each(function (i, fila) {
            $url= $("#isi_lnk_borrarRegs").attr('href') + '/' + fila.value;
	        // $url='/direccion/falsa/3';
            $objXhr = $.ajax({
                url: $url,
                method:'POST',
                async: false, /* falso = sincronico = 1 petición a la vez*/
                beforeSend:function(xhr) {
                    if (i == 0)
                        $("#isi_msjProcesando").removeClass("isi_quitarElemento");
                    $("#isi_msjPag").html("<br><div class='material-icons mdl-badge mdl-badge--overlap' data-badge=" + (i+1) + ">delete</div>");
                },
                success:function(response, status, request) {
                    $totRegi--;
                    if ((i+1) == $cantChks) { // cuando llego a la cantidad de item seleccionados oculto el mensaje, spin y los checks
                        $("#isi_msjProcesando").addClass("isi_quitarElemento");
                        $("#isi_msjPag").html("");
                        // $("#tituTLista span.mdl-badge").attr("data-badge", $totRegi);
                        alternarChkTodos();
                    }
                    $("#isi_fila_"+fila.value).remove(); // quito la fila del registro eliminado

                    if ($totRegi != null) // si hay badge
                        $("#tituTLista span.mdl-badge").attr("data-badge", $totRegi);
                },
                error:function(xhr, textStatus, errorThrown) {
                    $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                    $("#isi_msjProcesando").addClass("isi_quitarElemento");
                    return false;
                }
            });
        });

        // mostrar mensaje toast / snack si no hubo error o si no hubo acción ajax ($$objXhr.status = 0)
        if (($.inArray ($objXhr.status, [0, 200]) !== -1)) {
            $msj = "";
            switch (true) {
                case ($cantChks == 0) :
                $msj = "Elija algún elemento para eliminarlo";
                break;
                case ($cantChks == 1) :
                    $msj = "Se eliminó 1 registro";
                    break;
                case ($cantChks > 1) :
                    $msj = "Se eliminaron " + $cantChks + " registros";
                    break;
            };
            notification.MaterialSnackbar.showSnackbar({
                message: $msj
                , timeout: 2500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
			// Fin mostrar mensaje toast / snack si no hubo error o si no hubo acción ajax ($$objXhr.status = 0)

            if ($totRegi == 0) {// si eliminan todo recargo la pagina al final
                $("#tLista").remove();
                $("#busqEstCivil").remove();
            }
        }
    });

});
