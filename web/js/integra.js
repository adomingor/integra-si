$(document).ready(function() {
    // GLOBALES ------------------------------------------------------------------
        // <dialog> actualmente soportada por Chrome (experimental)
        // var dialog = document.querySelector('dialog');
        // dialogPolyfill.registerDialog(dialog);
        var $isi_msjErrSist = "Contacte al administrador del sitio!"; // agregar email, nro telef. etc.
        var $isi_msj_popUp = document.querySelector('.mdl-js-snackbar'); // para mostrar los mensajes snack/toast
        var objetoAjax = false; // Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error

        String.prototype.capitaliza = function() { // pasa la primera letra de la primera palabra a mayúsculas
            return this.charAt(0).toUpperCase() + this.slice(1);
        };
        String.prototype.titulo = function() { // pasa la primera letra de cada palabra a mayúsculas
            return this.toLowerCase().replace(/(^|\s)([a-z])/g, function(m, p1, p2) { return p1 + p2.toUpperCase(); });
        };

        function crearAjax(){
            if(navigator.appName=="Microsoft Internet Explorer") //Preguntando si nuestro querido usuario aún usa Internet Explorer
                objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
            else //De lo contrario está usando otro navegador, por supuesto uno mejor
                objetoAjax = new XMLHttpRequest();
            return(objetoAjax); //Retornamos nuestro objeto
        };
    // FIN GLOBALES ------------------------------------------------------------------


    function isi_abrirModal ($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id).css({"opacity":"1", "pointer-events":"auto"});
        return true
    };
    function isi_cerrarModal ($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id).css({"opacity":"", "pointer-events":""});
        return true
    };

    $(".isi_cerrarModal").click(function() {
        isi_cerrarModal(this.name);
        // $("#"+this.name).css({"opacity":"", "pointer-events":""});
    });

    $(".isi_abrirModal").click(function() {
        // $("#"+this.name).css({"opacity":"1", "pointer-events":"auto"});
        isi_abrirModal(this.name);
    });

    /* (des)chequea un grupo de input tipo checkbox que estén habilitados (disabled = false)
    el elemento debe contener la clase isi_chk_grupo
    y en el name el nombre del grupo de check que quiere controlar
    si no tiene name o es "" chequea todos los checks
    */
    $("input[type=checkbox].isi_chk_grupo").click(function(elemento) {
        return (tildarCheck(this.name, this.checked));
    });
    function tildarCheck($nombre, $check) { // $nombre = "[name=NombreGrupoA(des)Tildar]" o "", $check = true o false
        $nombre.length ? $name = "[name="+$nombre+"]" : $name = "";
        // $("input:checkbox"+$name+":not(:disabled)").prop('checked', $check); // check comunes
        $("input:checkbox:not(:disabled)"+$name).prop('checked', $check); // check comunes
        $.each($("label.mdl-checkbox"+$name), function (indice, elemento) { // check mdl
            if (!elemento.firstElementChild.disabled)
                $check ? elemento.MaterialCheckbox.check(): elemento.MaterialCheckbox.uncheck();
        });
    }
    /* Fin (des)chequea un grupo de input tipo checkbox */

    // muestra un mensaje con la cantidad de checkbox seleccionados que no sean cabeceras de checks
    // si el check tildado no es cabecera y tiene name (de algun grupo posiblemente), me fijo si hay
    // mas check con ese nombre que no sean cabecera, si no hay mas y existe una cabecera, la destildo
    $("input:checkbox").click(function(elemento) {
        $chks  = $('input:checkbox:checked:not(.isi_chk_grupo)');

        if ($(this).attr("name")) {
            if ($("input:checkbox:checked:not(.isi_chk_grupo):not(:disabled)[name="+this.name+"]").length == 0)
                tildarCheck(this.name, false);
        }

        $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Hay " + $chks.length + " elemento(s) seleccionado(s)", timeout: 1000});
    });

    // function alternarChkTodos() {
    //     desTildarMultiCheck("isi_inpChk_todos"); // destildo cabecera
    //     desTildarMultiCheck("isi_lbl_chkMultiAccion"); // destildo resto multicheck
    //     $("[name='isi_td_verSiNo']").toggleClass("isi_ocultar");
    //     if ($("[name='isi_td_verSiNo']").hasClass("isi_ocultar"))
    //         $("#isi_lnk_verAllChk").html("Mostrar MultiCheck");
    //     else
    //         $("#isi_lnk_verAllChk").html("Ocultar MultiCheck");
    // };
    //
    // $("#isi_lnk_verAllChk").click(function(evento) {
    //     alternarChkTodos();
    // });

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

    /* Eliminar registros de una tabla */
    /* el objeto que llama a la accion debe tener:
    class = "isi_elim_reg"; name="grupo de checkbox de la tabla" */
    /*la tabla (html) debe tener:
    tr con id = value del checkbox que tiene la fila (tr)
    checkbox del tr con value = id del registro a eliminar*/
    /* Debe existir un objeto con el name="grupo de checkbox de la tabla" y un valor = total general de registros
        en este caso se usa un badge de mdl
    */
    $(".isi_elim_reg").click(function(elemento){
        // controles:
        // el objeto debe tener la propiedad name (con el nombre del grupo de checkbox) y el href a la acción del controlador
        // la fila debe estar visible (por si está activo el filtro de busqueda), el checkbox habilitado
        // debe haber al menos un check seleccionado y habilitado
        if (!$(this).attr("name")) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Imposible determinar los registros a eliminar. " + $isi_msjErrSist, timeout: 2500});
            return false;
        }
        if (!$(this).attr("href")) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Imposible ejecutar la acción. " + $isi_msjErrSist, timeout: 2500});
            return false;
        }
        var $totRegi = $("span.mdl-badge[name="+this.name+"]").attr("data-badge"); //null = undefined = no hay badge
        if(!$totRegi) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Imposible obtener la cantidad de registros. " + $isi_msjErrSist, timeout: 2500});
            return false;
        }
        var $cantChks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name="+this.name+"]").length;
        if ($cantChks == 0) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Elija un elemento para eliminarlo", timeout: 2500});
            return false;
        }

        $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Pasa los controles", timeout: 1000});


        isi_abrirModal("isi_div_msjConfirmacion");
        evento.preventDefault();

        if ($("#isi_btn_confAccion").click)
            alert("si");

        //confirm("Press a button!");
        //chkg1

        return true;
    });


    /* Elimina los registros marcados con el check en una (ver listado de estado civil)  */
    $("#isi_lnk_borrarRegs").click(function(evento) {
        evento.preventDefault();
        var $cantChks = $("input[name='isi_inpChk_MultiAccion']:checked").length;
        var $totRegi = $("#tituTLista span.mdl-badge").attr("data-badge"); //null = undefined = no hay badge

        // verificamos que este visible la columna de selección múltiple
        if ($("[name='isi_td_verSiNo']").hasClass("isi_ocultar")) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Active la opción 'MultiCheck'", timeout: 2500 // msegs
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
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: $msj, timeout: 2500});
			// Fin mostrar mensaje toast / snack si no hubo error o si no hubo acción ajax ($$objXhr.status = 0)

            if ($totRegi == 0) { // si eliminan todo recargo la pagina al final
                $("#isi_tbl_listado").remove(); // quito la tabla del listado vacio
                $("#isi_div_busqListado").remove(); // si hay busqueda en la tabla del listado la elimino
            }
        }
    });
});
