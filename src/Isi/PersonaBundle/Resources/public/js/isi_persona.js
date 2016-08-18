var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';
// control para casilla nn (esta contemplada en la de abajo si no uso esta)
$("#dnies_numero").focusout (function (e) {
    var $dniMin = 4000000,
        $dniMax = 100000000;
    if ($(this).val() <= 0)
        $("#dnies_personas_nn").prop("checked", true);
    else
        $("#dnies_personas_nn").prop("checked", false);
});
// /* la funcion de abajo es para validar antes de grabar, se puede redireccionar a
// modificacion de personas */
// $("#dnies_numeroXX").focusout (function (e) {
//     var $dniMin = 4000000,
//         $dniMax = 100000000;
//     if ($(this).val() == 0) {
//         $("#isi_btnGrabar").attr("disabled", false);
//         $("#dnies_personas_nn").prop("checked", true);
//     } else if (($(this).val() >= $dniMin) && ($(this).val() <= $dniMax)) {
//         $("#dnies_personas_nn").prop("checked", false);
//         $.get($("#isi_ctrlAltaPers").val() + "/" + $(this).val())
//         .done(function( data ) {
//             if (data.existe == "true") {
//                 swal({
//                   title: "Ya existe el D. N. I.",
//                   type: "warning",
//                   text: $ayuda,
//                   timer: $isi_tiemMsjMedio
//                 });
//                 $("#isi_btnGrabar").attr("disabled", true);
//             }
//             else {
//                 $("#isi_btnGrabar").attr("disabled", false);
//             }
//         });
//     } else {
//         swal({
//           title: "No es un D. N. I. válido",
//           type: "error",
//           text: "<span class='text-info'>Valores permitidos 0, o entre " + $dniMin + " y " + $dniMax + "</span>",
//           timer: $isi_tiemMsjMedio
//         });
//         $("#isi_btnGrabar").attr("disabled", true);
//         $("#dnies_numero").focus();
//     }
// });

// limpia la image y elimina la informacion de la misma
function limpiarImg() {
    $img = window.location.pathname.substr(0, window.location.pathname.indexOf("b/") + 2) + "imagenes/sin_foto.png";
    $("#isi_imagenBuscar").filestyle('clear'); // limpio el texto del boton buscar imagen
    $("#isi_imagen").attr("src", $img); // quito la imagen del tag <img>
    $("#isi_infoImg").html(""); // limpio si hay mensaje informativo sobre la imagen
    $("#dnies_personas_foto").val(""); // limpio el input para grabar la foto de la persona en la bd
    btnElimImagen("hidden"); // oculto el boton para limpiar la foto
};

// muestra/oculta el boton para limpiar la foto
function btnElimImagen($ver) {
    $("#isi_btnElimFoto").css("visibility", $ver);
}

// Carga una imagen desde almacenamiento
$("#isi_imagenBuscar").change(function(e) {
    agregarImagen(e);
});

function agregarImagen(e){
    var file = e.target.files[0],
    imageType = /image.*/,
    maxTam = 2097152; //= 2 mb

    // controles
    if (!file.type.match(imageType)) {
        limpiarImg();
        $("#isi_infoImg").html("no es una imágen");
        return false;
    }

    if (file.size > maxTam) {
        limpiarImg();
        $("#isi_infoImg").html("la imágen tiene q ser menor a 2MB");
        return false;
    }
    // Fin controles

    $("#isi_infoImg").html("");
    var reader = new FileReader();
    reader.onload = cargarArchivo;
    reader.readAsDataURL(file);
    btnElimImagen("visible");
    return true;
};

function cargarArchivo(e) {
    var result=e.target.result;
    $("#isi_imagen").attr("src",result);
    $("#dnies_personas_foto").val(result);
};
// Fin Carga una imagen desde almacenamiento

// Agrega los id de las pesonas seleccionadas para trabajar
$("#isi_selPersTrab").click(function(elemento) {
    elemento.preventDefault();
    if (($("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=" + this.name + "]").length) == 0) {
        swal({
          title: "Seleccione&nbsp;&nbsp;<i class='fa fa-check-square-o fa-lg text-info' aria-hidden='true'></i>",
          type: "warning",
          text: "Los datos con los que desea trabajar <i class='fa fa-users fa-lg text-success' aria-hidden='true'></i>",
          timer: $isi_tiemMsjCorto
        });
        return false;
    }

    var $Chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=" + this.name + "]");
    var lc_ids = "";
    var band = true;
    $.each($($Chks), function (indice, elemento) {
        if (band) {
            lc_ids = elemento.value.trim();
            band = false;
        }
        else
            lc_ids = lc_ids + "¬" + elemento.value.trim();
    });

    $.get(document.activeElement.href + "/" + lc_ids)
    .done(function( data ) {
        window.setTimeout( function() {
            swal({
              title: "Guardados",
              type: "success",
              text: "<i class='fa fa-thumbs-o-up fa-2x text-success' aria-hidden='true'></i> Puedes trabajar con las personas seleccionadas",
              timer: $isi_tiemMsjMedio
            }).then(function() {
                 window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
            }, function(dismiss) {
              window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
            });
        }, $isi_tiemRecargaCorto);
    });
});
// Fin Agrega los id de las pesonas seleccionadas para trabajar
