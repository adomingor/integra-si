// GLOBALES ------------------------------------------------------------------
    // <dialog> actualmente soportada por Chrome (experimental)
    // var dialog = document.querySelector('dialog');
    // dialogPolyfill.registerDialog(dialog);

    var isi_msj_popUp = document.querySelector('.mdl-js-snackbar');
    /* Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error */
    var objetoAjax = false;

    String.prototype.capitaliza = function() { // pasa la primera letra de la primera palabra a mayúsculas
        return this.charAt(0).toUpperCase() + this.slice(1);
    };
    String.prototype.titulo = function() { // pasa la primera letra de cada palabra a mayúsculas
        return this.toLowerCase().replace(/(^|\s)([a-z])/g, function(m, p1, p2) { return p1 + p2.toUpperCase(); });
    };

    function crearAjax(){
        //Preguntando si nuestro querido usuario aún usa Internet Explorer
        if(navigator.appName=="Microsoft Internet Explorer")
            objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
        else //De lo contrario está usando otro navegador, por supuesto uno mejor
            objetoAjax = new XMLHttpRequest();
        return(objetoAjax); //Retornamos nuestro objeto
    };

// FIN GLOBALES ------------------------------------------------------------------

$(document).ready(function() {
    $(".isi_cerrarModal").click(function() {
        $("#"+this.name).css("opacity", "");
        $("#"+this.name).css("pointer-events", "");
    });

    $(".isi_abrirModal").click(function() {
        $("#"+this.name).css("opacity", "1");
        $("#"+this.name).css("pointer-events", "auto");
    });

    // /* (des)chequear TODOS input tipo checkbox
    // El elemento debe contener la clase isi_chk_todos
    // */
    // $("input[type=checkbox].isi_chk_todos").click(function(objeto) {
    //     $("input[type=checkbox]:not(:disabled)").prop('checked', this.checked); // check comunes
    //     if (this.checked) { // check mdl <- falta ver si esta habilitado
    //         $.each($("label.mdl-checkbox"), function (indice, elemento) {
    //             if (!elemento.disabled)
    //                 elemento.MaterialCheckbox.check();
    //         });
    //     }
    //     else {
    //         $.each($("label.mdl-checkbox"), function (indice, elemento) { elemento.MaterialCheckbox.uncheck(); });
    //     }
    // });

    /* (des)chequea un grupo de input tipo checkbox que estén habilitados (disabled = false)
    el elemento debe contener la clase isi_chk_grupo
    y en el name el nombre del grupo de check que quiere controlar
    si no tiene name o es "" chequea todos los checks
    */
    $("input[type=checkbox].isi_chk_grupo").click(function(elemento) {
         // controlo que posea attr name ($(this).attr("name") ? "[name="+this.name+"]" : "")
        $("input:checkbox"+($(this).attr("name") ? "[name="+this.name+"]" : "")+":not(:disabled)").prop('checked', this.checked); // check comunes

        // ------------------------- check mdl
        if (this.checked)
            $.each($("label.mdl-checkbox"+($(this).attr("name") ? "[name="+this.name+"]" : "")), function (indice, elemento) {
                if (!elemento.firstElementChild.disabled)
                    elemento.MaterialCheckbox.check();
            });
        else
            $.each($("label.mdl-checkbox"+($(this).attr("name") ? "[name="+this.name+"]" : "")), function (indice, elemento) {
                if (!elemento.firstElementChild.disabled)
                    elemento.MaterialCheckbox.uncheck(); });
        // ------------------------- fin check mdl
    });

    // cuando activan la busqueda, oculto todos las filas de la tabla que no coincidan con la busqueda
    $("#isi_inpTxt_buscar").keyup(function(evento) {
        $.each($("tr[name='isi_tr_tbl_listado']"), function (indice, elemento) {
            if (elemento.attributes.value.value.indexOf($("#isi_inpTxt_buscar").val().toLowerCase()) > -1)
                $("#" + elemento.attributes.id.value).show();
            else
                $("#" + elemento.attributes.id.value).hide();
        });
        $("#tituTLista span.mdl-badge").attr("data-badge", $("tr[name='isi_tr_tbl_listado']").not(".isi_ocultar").length);
    });

    // activa el buscador (listado en una tabla)
    $("#isi_lnk_verBusc").click(function(evento) {
        $("#isi_inpTxt_buscar").focus();
    });

    function tildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (indice, elemento) {
            elemento.MaterialCheckbox.check();
        });
    };

    function desTildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (indice, elemento) {
            elemento.MaterialCheckbox.uncheck();
        });
    };

    $("#isi_inpChk_todos").click(function(evento) {
        if ($("#isi_inpChk_todos").length) { // si existe en la pagina el checkbox de (des)Tildar todos
            if (this.checked)
                tildarMultiCheck("isi_lbl_chkMultiAccion");
            else
                desTildarMultiCheck("isi_lbl_chkMultiAccion"); // destildo resto multicheck
        }
    });

    function alternarChkTodos() {
        desTildarMultiCheck("isi_inpChk_todos"); // destildo cabecera
        desTildarMultiCheck("isi_lbl_chkMultiAccion"); // destildo resto multicheck
        $("[name='isi_td_verSiNo']").toggleClass("isi_ocultar");
        if ($("[name='isi_td_verSiNo']").hasClass("isi_ocultar"))
            $("#isi_lnk_verAllChk").html("Mostrar MultiCheck");
        else
            $("#isi_lnk_verAllChk").html("Ocultar MultiCheck");
    };

    $("#isi_lnk_verAllChk").click(function(evento) {
        alternarChkTodos();
    });

    /* Elimina los registros marcados con el check en una (ver listado de estado civil)  */
    $("#isi_lnk_borrarRegs").click(function(evento) {
        evento.preventDefault();
        var $cantChks = $("input[name='isi_inpChk_MultiAccion']:checked").length;
        var $totRegi = $("#tituTLista span.mdl-badge").attr("data-badge"); //null = undefined = no hay badge

        // verificamos que este visible la columna de selección múltiple
        if ($("[name='isi_td_verSiNo']").hasClass("isi_ocultar")) {
            isi_msj_popUp.MaterialSnackbar.showSnackbar({
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

        $("input[name='isi_inpChk_MultiAccion']:checked").each(function (indice, elemento) {
            $objXhr = $.ajax({
                url: $("#isi_lnk_borrarRegs").attr('href') + '/' + elemento.value,
                method:'POST',
                async: false, /* falso = sincronico = 1 petición a la vez*/
                beforeSend:function(xhr) {
                    if (indice == 0)
                        $("#isi_msjProcesando").removeClass('isi_ocultar');
                    $("#isi_msjPag").html("<br><div class='material-icons mdl-badge mdl-badge--overlap' data-badge=" + (indice + 1) + ">delete</div>");
                },
                success:function(response, status, request) {
                    $totRegi--;
                    if ((indice + 1) == $cantChks) { // cuando llego a la cantidad de item seleccionados oculto el mensaje, spin y los checks
                        $("#isi_msjProcesando").addClass('isi_ocultar');
                        $("#isi_msjPag").html("");
                        alternarChkTodos();
                    }
                    $("#isi_tr_tbl_lis_"+elemento.value).remove(); // quito la fila de la tabla del registro eliminado

                    if ($totRegi != null) // si hay badge
                        $("#tituTLista span.mdl-badge").attr("data-badge", $totRegi);
                },
                error:function(xhr, textStatus, errorThrown) {
                    $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                    $("#isi_msjProcesando").addClass('isi_ocultar');
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
            isi_msj_popUp.MaterialSnackbar.showSnackbar({
                message: $msj
                , timeout: 2500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
			// Fin mostrar mensaje toast / snack si no hubo error o si no hubo acción ajax ($$objXhr.status = 0)

            if ($totRegi == 0) { // si eliminan todo recargo la pagina al final
                $("#isi_tbl_listado").remove(); // quito la tabla del listado vacio
                $("#isi_div_busqListado").remove(); // si hay busqueda en la tabla del listado la elimino
            }
        }
    });
});
