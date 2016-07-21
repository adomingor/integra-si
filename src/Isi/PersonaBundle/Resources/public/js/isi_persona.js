var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';
// control para no duplicar dnies
$("#dnies_numero").focusout (function (e) {
    var $dniMin = 4000000,
        $dniMax = 100000000;
    if ($(this).val() == 0) {
        $("#isi_btnGrabar").attr("disabled", false);
        $("#dnies_personas_nn").prop("checked", true);
    } else if (($(this).val() > $dniMin) && ($(this).val() < $dniMax)) {
        $("#dnies_personas_nn").prop("checked", false);
        $.get($("#isi_ctrlAltaPers").val() + "/" + $(this).val())
        .done(function( data ) {
            if (data.existe == "true") {
                swal({
                  title: "Ya existe el D. N. I.",
                  type: "warning",
                  text: $ayuda,
                  timer: $isi_tiemMsjMedio
                });
                $("#isi_btnGrabar").attr("disabled", true);
            }
            else {
                $("#isi_btnGrabar").attr("disabled", false);
            }
        });
    } else {
        swal({
          title: "No es un D. N. I. válido",
          type: "error",
          text: "<span class='text-info'>Valores permitidos 0, o entre " + $dniMin + " y " + $dniMax + "</span>",
          timer: $isi_tiemMsjMedio
        });
        $("#isi_btnGrabar").attr("disabled", true);
        $("#dnies_numero").focus();
    }
});

// limpia la image y elimina la informacion de la misma
function limpiarImg() {
    $img = window.location.pathname.substr(0, window.location.pathname.indexOf("b/") + 2) + "imagenes/sin_foto.png";
    $("#isi_imagen").attr("src", $img);
    $("#isi_infoImg").html("");
    $("#dnies_personas_foto").val("");
};

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
    return true;
};

function cargarArchivo(e) {
    var result=e.target.result;
    $("#isi_imagen").attr("src",result);
    $("#dnies_personas_foto").val(result);
};
// Fin Carga una imagen desde almacenamiento
