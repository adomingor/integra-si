$(document).ready(function() {
// GLOBALES ------------------------------------------------------------------
    // <dialog> actualmente soportada por Chrome (experimental)
    // var dialog = document.querySelector('dialog');
    // dialogPolyfill.registerDialog(dialog);
    var $isi_msjErrSist = "Contacte al administrador del sitio!"; // agregar email, nro telef. etc.
    var $isi_msj_popUp = document.querySelector('.mdl-js-snackbar'); // para mostrar los mensajes snack/toast
    var objetoAjax = false; // Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error
    var $isi_elmi_regi = null; // Guarda el objeto que llama a eliminar los registros

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
        $("#"+$id+".isi_modal").css({"opacity":"1", "pointer-events":"auto"});
        $("#"+$id+".isi_modal").addClass("animated bounceInDown");
        window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceInDown")}, 1300);
        return true
    };
    function isi_cerrarModal ($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id+".isi_modal").css({"opacity":"", "pointer-events":""});
        $("#"+$id+".isi_modal").addClass("animated bounceOutDown");
        window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceOutDown")}, 1300);
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

    /*filtros de busqueda
    el elemento que ocultara los values que no coincidan con la busqueda debe tener
    class="isi_filtrar-busqueda" name="grupo de elementos a comparar values"
    los elementos a ocultar deben tener la clase isi_filtrable y el value = dato a filtrar
    */
    $(".isi_filtrar-busqueda").keyup(function(evento) {
        var $filtro = this.value.toLowerCase();
        $.each($(".isi_filtrable[name="+this.name+"]"), function (indice, elemento) {
            if (elemento.attributes.value.value.indexOf($filtro) > -1)
                $(elemento).removeClass("isi_ocultar");
            else
                $(elemento).addClass("isi_ocultar");
        });
    });


    /*muestra/oculta elementos con class="isi_ocultable"
    el elemento que llama a la funcion debe contener el name del grupo a mostrar/ocultar
    y class="isi_ocultable-cambio"
    */
    $(".isi_ocultable-cambio").click(function(elemento) {
        $(".isi_ocultable[name="+this.name+"]").toggleClass("isi_ocultar");
        if ($(this).html()=="Ocultar MultiCheck")
            $(this).html("Mostrar MultiCheck");
        else
            $(this).html("Ocultar MultiCheck");
    });

    /* (des)chequea un grupo de input tipo checkbox que estén habilitados (disabled = false)
    el elemento debe contener la clase isi_chk_grupo
    y en el name el nombre del grupo de check que quiere controlar
    si no tiene name o es "" chequea todos los checks
    */
    $("input[type=checkbox].isi_chk_grupo").click(function(elemento) {
        tildarCheck(this.name, this.checked);
        $.each($("input:checkbox.isi_chk_grupo"), function (indice, elemento) {
            if (elemento.name) {
                isi_ctrlChkCab_badge(elemento.name);
            }
        });
        // return (tildarCheck(this.name, this.checked));
    });
    function tildarCheck($nombreGrupo, $check) { // $nombreGrupo = "[name=NombreGrupoA(des)Tildar]" o "", $check = true o false
        $nombreGrupo.length ? $name = "[name="+$nombreGrupo+"]" : $name = "";
        $("input:checkbox:not(.isi_ocultar):not(.mdl-checkbox__input):not(:disabled)"+$name).prop('checked', $check); // check comunes (no chk mdl)
        $.each($("label.mdl-checkbox"+$name), function (indice, elemento) { // check mdl
            if (!elemento.firstElementChild.disabled)
                if (!$(elemento).hasClass("isi_ocultar"))
                    $check ? elemento.MaterialCheckbox.check(): elemento.MaterialCheckbox.uncheck();
        });
        return true;
    }
    /* Fin (des)chequea un grupo de input tipo checkbox */

    // si el check tildado no es cabecera y tiene name (de algun grupo posiblemente), me fijo si hay
    // mas check con ese nombre que no sean cabecera, si no hay mas y existe una cabecera, la destildo
    // si estan seleccionados todos, marco la cabecera
    function isi_ctrlChkCab_badge($nombreGrupo) {
        $cantGrupo = $("input:checkbox:not(.isi_chk_grupo):not(:disabled)[name="+$nombreGrupo+"]").length;
        $cantChkGrupo = $("input:checkbox:checked:not(.isi_chk_grupo):not(:disabled)[name="+$nombreGrupo+"]").length;
        if ($cantChkGrupo == 0) // si es el sultimo check de un grupo
            tildarCheck($nombreGrupo, false); // destildo la cabecera
        if ($cantGrupo == $cantChkGrupo)
            tildarCheck($nombreGrupo, true); // tildo la cabecera
        // actualizo la cantidad de checkbox seleccionados del grupo
        $("#isi_totChkSel_"+$nombreGrupo).attr("data-badge", $cantChkGrupo);
    };

    // muestra un mensaje con la cantidad de checkbox seleccionados que no sean cabeceras de checks
    // si tiene name llama a la funcion de actualizar el data-badge del grupo
    $("input:checkbox").click(function(elemento) {
        // cantidad de checkbox seleccionados que no son cabeceras ((des)tildadores)
        // $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Se encontraron " + $('input:checkbox:checked:not(.isi_chk_grupo)').length + " elemento(s) seleccionado(s)", timeout: 1000});
        if ($(this).attr("name")) {
            isi_ctrlChkCab_badge($(this).attr("name"));
        }
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
        elemento.preventDefault();
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
        if(!$("#isi_totRegi_"+this.name).attr("data-badge")) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Imposible obtener la cantidad de registros de la tabla. " + $isi_msjErrSist, timeout: 2500});
            return false;
        }
        if (($("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name="+this.name+"]").length) == 0) {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Elija un elemento para eliminarlo", timeout: 2500});
            return false;
        }
        if ($(".isi_ocultable-cambio[name="+this.name+"]").html()=="Mostrar MultiCheck") {
            $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Active la opción MultiCheck", timeout: 2500});
            return false;
        }
        // $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: "Pasa los controles", timeout: 1000});

        // solicitamos la confirmación del usuario para borrar
        // confirm("Desea eliminar los registros?");
        $isi_elmi_regi = $(this);
        $isi_msj_popUp.MaterialSnackbar.showSnackbar({
            message: "¿Eliminar los registros?"
            , timeout: 3500
            , actionHandler: isi_elim_reg_bd
            , actionText: "Eliminar"
            });

        return true;
    });

    // funcion que elimina los registros
    function isi_elim_reg_bd (evento) {
        if ($isi_elmi_regi != null) {
            $("#isi_msjToastSnack").removeClass("mdl-snackbar--active"); // quito el mensaje tast de eliminar
            var $totRegi = $("span.mdl-badge[name="+$isi_elmi_regi.attr("name")+"]").attr("data-badge"); //null = undefined = no hay badge
            var $Chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name="+$isi_elmi_regi.attr("name")+"]");
            var $objXhr = crearAjax(); // intentamos crear el objeto ajax
            if ($objXhr === false) {
                $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al crear el objeto ajax. <br> Contacte a Informática!.");
                return false;
            }
            $.each($($Chks), function (indice, elemento) {
                $objXhr = $.ajax({
                    url: $isi_elmi_regi.attr("href") + '/' + elemento.value,
                    method:'POST',
                    async: false, /* falso = sincronico = 1 petición a la vez*/
                    beforeSend:function(xhr) {
                        if (indice == 0)
                            $("#isi_msjProcesando").removeClass('isi_ocultar');
                        $("#isi_msjPag").html("<br><div class='material-icons mdl-badge mdl-badge--overlap' data-badge=" + (indice + 1) + ">delete</div>");
                    },
                    success:function(response, status, request) {
                        $totRegi--;
                        if ((indice + 1) == $Chks.length) { // cuando llego a la cantidad de item seleccionados oculto el mensaje, spin y los checks
                            $("#isi_msjProcesando").addClass('isi_ocultar');
                            $("#isi_msjPag").html("");
                        }
                        $("#isi_tr_"+$isi_elmi_regi.attr("name")+elemento.value).remove(); // quito la fila de la tabla del registro eliminado

                        if ($totRegi != null)  { // si hay badge
                            if ($("#isi_totRegi_"+$isi_elmi_regi.attr("name")).attr("data-badge")) {
                                $("#isi_totRegi_"+$isi_elmi_regi.attr("name")).attr("data-badge", $totRegi);
                                isi_ctrlChkCab_badge($isi_elmi_regi.attr("name"));
                            }
                        }
                    },
                    error:function(xhr, textStatus, errorThrown) {
                        $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                        $("#isi_msjProcesando").addClass('isi_ocultar');
                        return false;
                    }
                });
            });

            if (($.inArray ($objXhr.status, [0, 200]) !== -1)) { // si no hubo error
                $msj = "";
                switch (true) {
                    case ($Chks.length == 1) :
                        $msj = "Se eliminó 1 registro";
                        break;
                    case ($Chks.length > 1) :
                        $msj = "Se eliminaron " + $Chks.length + " registros";
                        break;
                };
                $isi_msj_popUp.MaterialSnackbar.showSnackbar({message: $msj, timeout: 1500});
    			// Fin mostrar mensaje toast / snack si no hubo error o si no hubo acción ajax ($$objXhr.status = 0)

                if ($totRegi == 0) { // si eliminan todo
                    $("#isi_tbl_"+$isi_elmi_regi.attr("name")).remove(); // quito la tabla del listado vacio
                    $("#isi_busqListad_"+$isi_elmi_regi.attr("name")).remove(); // si hay busqueda en la tabla del listado la elimino
                }
            }


            // alert("eliminados! tot grupo: " + $totRegi + ", eliminados: " + $Chks.length + " del grupo: " + $isi_elmi_regi.attr("name"));
        }
        else {
            alert("ups te falto asignar el objeto a eliminar");
            return false;
        }
        return true;
    };
});
