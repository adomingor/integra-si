var $isi_msjErrSist = "Contacte al administrador del sitio!"; // agregar email, nro telef. etc.
var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $isi_msjCancelado = "Operación cancelada";
var $isi_msjErrElim = "No se pudo eliminar";
var $tiposMsjSA2 = ["warning", "error", "success", "info", "question"];

/* Previsualización mensajes sweetAlert2*/
function verSA2($tipo, $titulo, $msj) {
    // se saca el substring a titulo y msj por que viene de twig con json_encode()
    // es cuando tiene cadenas largas el mensaje (con imagen base64 por ej.)
    if (!$tiposMsjSA2.includes($tipo))
        $tipo = "";
    swal({
        type: $tipo.toLowerCase(),
        title: $titulo != "null" ? $titulo.substring(1, $titulo.length - 1) : "",
        html: $msj != "null" ? $msj.substring(1, $msj.length - 1) : "",
        confirmButtonText: 'Aceptar',
        timer: $isi_tiemMsjMedio
    });
};

$(document).ready(function() {
// GLOBALES ------------------------------------------------------------------
    String.prototype.capitaliza = function() { // pasa la primera letra de la primera palabra a mayúsculas
        return this.charAt(0).toUpperCase() + this.slice(1);
    };
    String.prototype.titulo = function() { // pasa la primera letra de cada palabra a mayúsculas
        return this.toLowerCase().replace(/(^|\s)([a-z])/g, function(m, p1, p2) { return p1 + p2.toUpperCase(); });
    };

    $(".nav").slideAndSwipe();

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

    // trae la imagen del usuario para mostrarla PONER EN JS DE BUNDLE SESION
    $("#form_username").focusout(function(evento) {
        $url = $("#form_username").attr("src");
        $usr = $("#form_username").val();

        if ($usr.trim().length > 0) {
            $.get($url + "/" + $usr)
            .done(function( data ) {
                $img = window.location.pathname.substr(0, window.location.pathname.indexOf("b/") + 2) + "imagenes/avatar/sin_avatar.png";
                if (!$.trim(data) == 0) {
                    $("#isi_nomUsr").html("<p class='text-xs-center text-muted'>" + data[0].username.trim() + "</p>");
                    if (data[0].imagen.trim().length > 0)
                        $("#isi_imgUsr").html("<img class='media-object card-img-top img-circle m-x-auto' src='data:;base64, " + data[0].imagen.trim() + "'/>");
                    else
                        $('#isi_imgUsr').html("<img class='media-object card-img-top img-circle m-x-auto' src='" + $img +"'/>");
                }
                else {
                    $('#isi_imgUsr').html("<img class='media-object card-img-top img-circle m-x-auto' src='" + $img +"'/>");
                    $("#isi_nomUsr").html("<p class='text-xs-center text-info'> el usuario no existe </p>");
                }
            });
        }
        else {
            $('#isi_imgUsr').html("<img class='media-object card-img-top img-circle m-x-auto' src='" + $img +"'/>");
            $("#isi_nomUsr").html("<p class='text-xs-center text-info'> &nbsp; </p>");
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
    $.each($(".isi_msjFlash"), function (indice, elemento) {
        $msj = elemento.innerHTML.trim().split("¬"); // obtenemos el mensaje separados por ¬

        // controlamos que sea un tipo válido de mensaje
        if (!$tiposMsjSA2.includes($msj[0].trim().toLowerCase()))
            $msj[0] = "";

        // armamos el mensaje para mostrarlo (consultar https://limonte.github.io/sweetalert2/)
        swal({
            type: $msj[0].trim().toLowerCase(),
            title: $msj[1].trim(),
            html: $msj[2].trim(),
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: $isi_tiemMsj
        });
    });

// FIN GLOBALES ------------------------------------------------------------------
    // usado en formularios modales:
    // Estado Civil (a modo ejemplo)
    // Lugar de Nacimiento
    $("#formGrabarReg").submit(function(evento) {
        evento.preventDefault();
        $.post($(this).attr("action"), $(this).serialize())
            .always(function() {
                window.location.reload(true);
            });
    });

    function isi_abrirModal($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id+".isi_modal").css({"opacity":"1", "pointer-events":"auto"});
        $("#"+$id+".isi_modal").addClass("animated bounceIn");
        window.setTimeout( function(){ $("#" + $id + ".isi_modal").removeClass("animated bounceIn")}, 1300);
        // $("#"+$id+".isi_modal").addClass("animated bounceInDown");
        // window.setTimeout( function(){ $("#"+$id+".isi_modal").removeClass("animated bounceInDown")}, 1300);
        return true
    };
    function isi_cerrarModal($id) {
        //agregar control que exista .modal sino return false
        $("#"+$id+".isi_modal").css({"opacity":"", "pointer-events":""});
        $("#"+$id+".isi_modal").addClass("animated bounceOut");
        window.setTimeout( function(){ $("#" + $id + ".isi_modal").removeClass("animated bounceOut")}, 1300);
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
        $.each($(".isi_filtrable[name=" + this.name + "]"), function (indice, elemento) {
            if (elemento.innerHTML.toLowerCase().includes($filtro)) { // busca coincidencia en cualquier lugar del texto
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
        $(".isi_ocultable[name=" + this.name + "]").toggleClass("isi_ocultar");
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
        $nombreGrupo.length ? $name = "[name=" + $nombreGrupo + "]" : $name = "";
        $("input:checkbox:not(.isi_ocultar):not(:disabled)"+$name).prop('checked', $check);
        return true;
    }
    /* Fin (des)chequea un grupo de input tipo checkbox */

    // si el check tildado no es cabecera y tiene name (de algun grupo posiblemente), me fijo si hay
    // mas check con ese nombre que no sean cabecera, si no hay mas y existe una cabecera, la destildo
    // si estan seleccionados todos, marco la cabecera
    function isi_ctrlChkCab_badge($nombreGrupo) {
        $cantGrupo = $("input:checkbox:not(.isi_chk_grupo):not(:disabled)[name=" + $nombreGrupo + "]").length;
        $cantChkGrupo = $("input:checkbox:checked:not(.isi_chk_grupo):not(:disabled)[name=" + $nombreGrupo + "]").length;
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
    /* Elimina un registro desde el controlador*/
    $(".isi_elim_reg_ctrl").click(function(elemento) {
        elemento.preventDefault();
        swal({
            title: "¿Borrar éste dato?",
            text: "Ésta acción no puede ser revertida",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "<i class='fa fa-ban fa-2x' aria-hidden='true'></i>",
            confirmButtonText: "<i class='fa fa-trash-o fa-2x' aria-hidden='true'></i>",
            confirmButtonClass: "btn btn-danger",
            cancelButtonClass: "btn btn-secondary",
            timer: $isi_tiemMsjMedio
        }).then(function() {
            $.ajax({
                url: document.activeElement.href,
                method:"POST",
                async: false,
                beforeSend:function(xhr) {
                    swal({
                      showConfirmButton: false,
                      padding: 50,
                      html: "<i class='fa fa-spinner fa-pulse fa-4x fa-fw text-warning'></i> <span class='sr-only'>Loading...</span>"
                    });
                },
                success:function(response, status, request) {
                    swal({
                      title: "Dato eliminado!",
                      type: "success",
                      timer: $isi_tiemMsjCorto
                    }).then(function() {
                         window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                    }, function(dismiss) {
                      // dismiss can be 'cancel', 'overlay', 'close', 'timer'
                      window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                  });
                },
                error:function(xhr, textStatus, errorThrown) {
                    swal({
                      title: $isi_msjErrElim,
                      type: "error",
                      text: "<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i> Contacte al <i>administrador</i> del sistema",
                      timer: $isi_tiemMsjCorto
                    });
                }
            });
        }, function(dismiss) {
          // dismiss can be 'cancel', 'overlay', 'close', 'timer'
        //   if (dismiss === "cancel") {
              swal({
                  title: $isi_msjCancelado,
                  text: "",
                  type: "error",
                  timer: $isi_tiemMsjCorto
              });
        //   }
        });
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
    $(".isi_elim_reg").click(function(elemento) {
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
              text: "Imposible ejecutar la acción",
              timer: $isi_tiemMsjCorto
            });
            return false;
        }
        if(!$("#isi_totRegi[name=" + this.name + "]").hasClass("label-pill")) {
            swal({
              title: "Contacte al administrador&nbsp;&nbsp;<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i>",
              type: "error",
              text: "Imposible obtener la cantidad de registros del listado",
              timer: $isi_tiemMsjCorto
            });
            return false;
        }
        if (($("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=" + this.name + "]").length) == 0) {
            swal({
              title: "Seleccione&nbsp;&nbsp;<i class='fa fa-check-square-o fa-lg' aria-hidden='true'></i>",
              type: "info",
              text: "Los datos que desea <i class='fa fa-trash fa-lg text-danger' aria-hidden='true'></i>",
              timer: $isi_tiemMsjCorto
            });
            return false;
        }
        // solicitamos la confirmación del usuario para borrar
        swal({
            title: "¿Borrar los datos?",
            text: "Ésta acción no puede ser revertida",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "<i class='fa fa-ban fa-2x' aria-hidden='true'></i>",
            confirmButtonText: "<i class='fa fa-trash-o fa-2x' aria-hidden='true'></i>",
            confirmButtonClass: "btn btn-danger",
            cancelButtonClass: "btn btn-secondary",
            buttonsStyling: true,
            timer: $isi_tiemMsjMedio
        }).then(function() {
            swal.enableLoading(); // muestra el mismo mensaje con el boton girando hasta q se ejecuta el ejax
            isi_elim_reg_bd($(this));
        }, function(dismiss) {
          swal({
              title: $isi_msjCancelado,
              text: "",
              type: "error",
              timer: $isi_tiemMsjCorto
          });
          return false;
        });
        return true;
    });

    // funcion que elimina los registros
    function isi_elim_reg_bd(evento) {
        var $totRegi = $("#isi_totRegi[name=" + document.activeElement.name + "]").html(); //null = undefined = no hay badge
        var $Chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=" + document.activeElement.name + "]");
        var $band = true;
        $.each($($Chks), function (indice, elemento) {
            if ($band) {
                $.ajax({
                    url: document.activeElement.href + "/" + elemento.value,
                    method:"POST",
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
                        $("#isi_fila_"+ document.activeElement.name + elemento.value).remove(); // quito la fila de la tabla del registro eliminado
                        if ($totRegi != null)  { // si hay badge
                            $("#isi_totRegi[name="+ document.activeElement.name + "]").html($totRegi);
                            isi_ctrlChkCab_badge(document.activeElement.name);
                        }
                        if ($totRegi == 0) // si eliminan todo
                            $(".isi_listado[name="+ document.activeElement.name + "]").remove(); // quito la tabla del listado vacio
                    },
                    error:function(xhr, textStatus, errorThrown) {
                        $band = false;
                    }
                });
            } else {
                return false; // para q no siga iterando
            }
        });
        window.setTimeout( function() {
            if ($band) {
                swal({
                    title: "Dato eliminado!",
                    type: "success",
                    html: "Total de datos borrados: <span class='text-danger'>" + $Chks.length + "</span>",
                    timer: $isi_tiemMsjCorto
                }).then(function() {
                     window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                }, function(dismiss) {
                  window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                });
            } else {
                swal({
                    title: $isi_msjErrElim,
                    type: "error",
                    text: "<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i> Contacte al <i>administrador</i> del sistema",
                    timer: $isi_tiemMsjCorto
                }).then(function() {
                     window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                }, function(dismiss) {
                  window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
                });
            }
        }, $isi_tiemRecargaCorto);
    };

    // quita una persona con la que trabaja el usuario
    $(".isi_elimPersSelecTrab").click(function(elemento) {
        elemento.preventDefault();
        $.get(document.activeElement.href)
        .done(function( data ) {
             window.setTimeout( function() { window.location.reload(); }, $isi_tiemRecargaCorto);
        });
    });
});
