/* Previsualización mensajes sweetAlert2*/
function verSA2($tipo, $titulo, $msj) {
    swal({
        type: $tipo.toLowerCase(),
        title: $titulo,
        html: $msj,
        confirmButtonText: 'Aceptar'
    });
    // title: $titulo.toLowerCase(),
};

$(document).ready(function() {
// GLOBALES ------------------------------------------------------------------
    var $isi_msjErrSist = "Contacte al administrador del sitio!"; // agregar email, nro telef. etc.
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

    $('.nav').slideAndSwipe();

    $(document).keyup(function(evento){ // presión de teclas en la página
        // alert(evento.which);
        if(evento.which==27) // escape
        {
            $("#btn_menu").click();
            if ($("#isi_menu").hasClass("ssm-nav-visible"))
                $("#isi_busk-menu").focus();
            else
                $("#isi_busk-menu").val(""); // limpio la busqueda cuando se oculta el menu
        }
    });

    $(".input-daterange").datepicker({
        format: "dd-mm-yyyy",
        startView: 2,
        todayBtn: true,
        language: "es",
        daysOfWeekHighlighted: "0,6",
        autoclose: true,
        todayHighlight: true,
        toggleActive: true
    });

    /* utilizado para mostrar los mensajes flash de symfony con sweetAlert2*/
    if ( $("#isi_msjFlash").length > 0 ) { // si hay mensajes en la pagina
        $msj = $("#isi_msjFlash").html().split("¬"); // obtenemos el mensaje separados por ¬
        // armamos el mensaje para mostrarlo (consultar https://limonte.github.io/sweetalert2/)
        swal({
            type: $msj[0].trim().toLowerCase(),
            title: $msj[1].trim(),
            html: $msj[2].trim(),
            confirmButtonText: 'Aceptar'
        });
    };

// FIN GLOBALES ------------------------------------------------------------------

    // usado en formularios modales:
    // Estado Civil (a modo ejemplo)
    // Lugar de Nacimiento
    $('#formGrabarReg').submit(function(evento) {
        evento.preventDefault();
        var $objXhr = crearAjax(); // intentamos crear el objeto ajax
        if ($objXhr === false) {
            swal({
              title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
              type: "error",
              html: "Ups! ocurrió un error al crear el objeto ajax",
              timer: 4000
            });
            return false;
        }

        $objXhr = $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            async: false
        });
        // window.setTimeout( function(){ window.location.reload(true) }, 500);
        window.location.reload(true);
    });

    function isi_abrirModal($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id+".isi_modal").css({"opacity":"1", "pointer-events":"auto"});
        $("#"+$id+".isi_modal").addClass("animated bounceIn");
        window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceIn")}, 1300);
        // $("#"+$id+".isi_modal").addClass("animated bounceInDown");
        // window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceInDown")}, 1300);
        return true
    };
    function isi_cerrarModal($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id+".isi_modal").css({"opacity":"", "pointer-events":""});
        $("#"+$id+".isi_modal").addClass("animated bounceOut");
        window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceOut")}, 1300);
        // $("#"+$id+".isi_modal").addClass("animated bounceOutDown");
        // window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceOutDown")}, 1300);
        return true
    };

    $(".isi_cerrarModal").click(function() {
        isi_cerrarModal(this.name);
    });

    $(".isi_abrirModal").click(function() {
        isi_abrirModal(this.name);
    });

    /* el input debe tener la clase inf_filtrar oculta los div que tengan label
    con la clase inf_filtrable y que el contenido html no coincidan con lo
    ingresado. El label tiene el mismo name que el input para obtener el grupo
    a filtrar. El label tiene el for igual al id del div a filtrar
    */
    $(".isi_filtrar").keyup(function(evento) {
        var $filtro = this.value.toLowerCase();
        $.each($(".isi_filtrable[name="+this.name+"]"), function (indice, elemento) {
            if (elemento.innerHTML.toLowerCase().contains($filtro)) { // busca coincidencia en cualquier lugar del texto
                $("#"+elemento.htmlFor).show();
                $("#"+elemento.htmlFor).find("td input:checkbox").removeClass("isi_ocultar") //para check dentro de tablas, que tilde solo los visibles
            }
                else {
                $("#"+elemento.htmlFor).hide();
                $("#"+elemento.htmlFor).find("td input:checkbox").addClass("isi_ocultar")
            }
        });
    });

    /*muestra/oculta elementos con class="isi_ocultable"
    el elemento que llama a la funcion debe contener el name del grupo a mostrar/ocultar
    y class="isi_ocultable-cambio"
    */
    $(".isi_ocultable-cambio").click(function(elemento) {
        $(".isi_ocultable[name="+this.name+"]").toggleClass("isi_ocultar");
        // if ($(this).html()=="Ocultar MultiCheck")
        //     $(this).html("Mostrar MultiCheck");
        // else
        //     $(this).html("Ocultar MultiCheck");
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
    });
    function tildarCheck($nombreGrupo, $check) { // $nombreGrupo = "[name=NombreGrupoA(des)Tildar]" o "", $check = true o false
        $nombreGrupo.length ? $name = "[name="+$nombreGrupo+"]" : $name = "";
        $("input:checkbox:not(.isi_ocultar):not(:disabled)"+$name).prop('checked', $check);
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
        $("#isi_totChkSel_"+$nombreGrupo).html($cantChkGrupo);
    };

    // muestra un mensaje con la cantidad de checkbox seleccionados que no sean cabeceras de checks
    // si tiene name llama a la funcion de actualizar el data-badge del grupo
    $("input:checkbox").click(function(elemento) {
        // cantidad de checkbox seleccionados que no son cabeceras ((des)tildadores)
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
            return false;
        }
        if (!$(this).attr("href")) {
            swal({
              title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
              type: "error",
              html: "Imposible ejecutar la acción",
              timer: 4000
            });
            return false;
        }
        if(!$("#isi_totRegi[name="+this.name+"]").hasClass("label-pill")) {
            swal({
              title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
              type: "error",
              html: "Imposible obtener la cantidad de registros del listado",
              timer: 4000
            });
            return false;
        }
        if (($("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name="+this.name+"]").length) == 0) {
            swal({
              title: "Seleccione&nbsp;&nbsp;<i class='fa fa-check-square-o fa-lg' aria-hidden='true'></i>",
              type: "info",
              html: "Los datos que desea <strong class='text-danger'>eliminar&nbsp;<i class='fa fa-trash' aria-hidden='true'></i></strong>"
            });
            return false;
        }

        $isi_elmi_regi = $(this); // obtengo el objeto al que se le hizo click

        // solicitamos la confirmación del usuario para borrar
        swal({
            title: '¿Borrar los datos?',
            text: "Ésta acción no puede ser revertida",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: '<i class="fa fa-ban fa-2x" aria-hidden="true"></i>',
            confirmButtonText: '<i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>',
            confirmButtonClass: 'btn btn-danger',
            cancelButtonClass: 'btn btn-secondary',
            buttonsStyling: true,
            allowOutsideClick: false
        }).then(function() {
            isi_elim_reg_bd($(this));
        }, function(dismiss) {
          // dismiss can be 'cancel', 'overlay', 'close', 'timer'
          if (dismiss === 'cancel') {
              swal(
                  '',
                  'Cancelaste la operación',
                  'error'
              );
          }
        });

        return true;
    });

    // funcion que elimina los registros
    function isi_elim_reg_bd(evento) {
        if ($isi_elmi_regi != null) {
            var $band = true;
            var $totRegi = $("#isi_totRegi[name="+$isi_elmi_regi.attr("name")+"]").html(); //null = undefined = no hay badge
            var $Chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name="+$isi_elmi_regi.attr("name")+"]");
            var $objXhr = crearAjax(); // intentamos crear el objeto ajax
            if ($objXhr === false) {
                swal({
                  title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
                  type: "error",
                  html: "Ups! ocurrió un error al crear el objeto ajax",
                  timer: 4000
                });
                return false;
            }
            $.each($($Chks), function (indice, elemento) {
                if ($band) {
                    $objXhr = $.ajax({
                        url: $isi_elmi_regi.attr("href") + '/' + elemento.value,
                        method:'POST',
                        async: false, /* falso = sincronico = 1 petición a la vez*/
                        beforeSend:function(xhr) {
                            if (indice == 0)
                                $("#isi_msjProcesando").removeClass('isi_ocultar');
                            $("#isi_msjPag").html("<br><i class='fa fa-trash fa-2x' aria-hidden='true'></i><span class='label label-pill label-info isi_badgeSobre'>"+(indice + 1)+"</span>");
                        },
                        success:function(response, status, request) {
                            $totRegi--;
                            if ((indice + 1) == $Chks.length) { // cuando llego a la cantidad de item seleccionados oculto el mensaje, spin y los checks
                                $("#isi_msjProcesando").addClass('isi_ocultar');
                                $("#isi_msjPag").html("");
                            }
                            $("#isi_fila_"+$isi_elmi_regi.attr("name")+elemento.value).remove(); // quito la fila de la tabla del registro eliminado

                            if ($totRegi != null)  { // si hay badge
                                $("#isi_totRegi[name="+$isi_elmi_regi.attr("name")+"]").html($totRegi);
                                isi_ctrlChkCab_badge($isi_elmi_regi.attr("name"));
                            }
                        },
                        error:function(xhr, textStatus, errorThrown) {
                            swal({
                              title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
                              type: "error",
                              html: "Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")",
                              timer: 4000
                            });
                            $("#isi_msjProcesando").addClass('isi_ocultar');
                            $band = false;
                        }
                    });
                }
            });

            if (($.inArray ($objXhr.status, [0, 200]) !== -1)) { // si no hubo error
                if ($totRegi == 0) // si eliminan todo
                    $(".isi_listado[name="+$isi_elmi_regi.attr("name")+"]").remove(); // quito la tabla del listado vacio

                swal(
                    '',
                    'Total de datos borrados: '+$Chks.length,
                    'success'
                );
            }
        }
        else {
            alert("ups te falto asignar el objeto a eliminar");
            return false;
        }
        return true;
    };
});
