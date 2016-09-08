var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';
// control para casilla nn (esta contemplada en la de abajo si no uso esta)

function base64Encode(str) {
    var CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var out = "", i = 0, len = str.length, c1, c2, c3;
    while (i < len) {
        c1 = str.charCodeAt(i++) & 0xff;
        if (i == len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt((c1 & 0x3) << 4);
            out += "==";
            break;
        }
        c2 = str.charCodeAt(i++);
        if (i == len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
            out += CHARS.charAt((c2 & 0xF) << 2);
            out += "=";
            break;
        }
        c3 = str.charCodeAt(i++);
        out += CHARS.charAt(c1 >> 2);
        out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += CHARS.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += CHARS.charAt(c3 & 0x3F);
    }
    return out;
}


// Agrega los id de las pesonas seleccionadas para trabajar
$("#isi_lstPersUsr").change(function(elemento) {
    // alert("hola");
    // location.href="http://10.10.3.1/integra-si/web/app_dev.php/admin/usuario/crear/333";
    location.href = this[this.selectedIndex].value;
    // $.get(document.activeElement.href + "/" + lc_ids)
    // .done(function( data ) {
    //     window.setTimeout( function() { window.location.reload(true); }, $isi_tiemRecargaCorto); // tengo q esperar por que sino aparece 2 veces el mensaje (nidea xq)
    // }
});
// Fin Agrega los id de las pesonas seleccionadas para trabajar

// Seleccion de avatar
$(".isi_img_SelAvatar").click(function(elemento) {
    $.ajax({
        url: elemento.currentTarget.src,
        type: "GET",
        headers: { "Authorization" : "Basic " +  btoa("user:pw") },
        xhrFields: { withCredentials: true },
        mimeType: "text/plain; charset=x-user-defined"
    }).done(function( data, textStatus, jqXHR ) {
        $img64 = base64Encode(data);
        $("#isi_img_usrAvatar").attr('src', 'data:image/jpeg;base64,' + $img64);
        $("#usuarios_imagen").val($img64);
        // $("#isi_img_usrAvatarNomb").val(elemento.currentTarget.src.substr(elemento.currentTarget.src.lastIndexOf('/') + 1));
        $("#isi_lnk_usrAvatar").click(); // cierro las imágenes (collapse)
    }).fail(function( jqXHR, textStatus, errorThrown ) {
        swal({
            title: "Ups!",
            text: "<i class='fa fa-bug fa-lg text-danger' aria-hidden='true'></i> No se pudo cargar la imágen <br> (" + errorThrown + ")",
            type: "error",
            timer: $isi_tiemMsjCorto
        });
    });
});
// fin Seleccion de avatar

function hexToRgb(hex) {
   var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
   return result ? {
       r: parseInt(result[1], 16),
       g: parseInt(result[2], 16),
       b: parseInt(result[3], 16)
   } : null;
}

function hex2rgb(hex, opacity) {
        var h=hex.replace('#', '');
        h =  h.match(new RegExp('(.{'+h.length/3+'})', 'g'));

        for(var i=0; i<h.length; i++)
            h[i] = parseInt(h[i].length==1? h[i]+h[i]:h[i], 16);

        if (typeof opacity != 'undefined')  h.push(opacity);

        return 'rgba('+h.join(',')+')';
}

// Menu personalizado
function colorMenuUsr($color, $opacidad, $letra) {
    $("#isi_img_usrAvatar").css("background", hex2rgb($color, $opacidad));
    $("#isi_menu").css("background", hex2rgb($color, $opacidad));
    $("a.list-group-item-action").css("background", hex2rgb($color, $opacidad));
    $("a.list-group-item-action").css("color", $letra);
}

// guarda los valores
function coloresUsr($color, $opacidad, $letra) {
    $("#usuarios_menu_color").val($color);
    $("#usuarios_menu_opacidad").val($opacidad);
    $("#usuarios_menu_color_letra").val($letra);
}

// color de fondo
$('#isi_colorMenu').on('input', function(e) {
    colorMenuUsr($(this).val(), $("#isi_opacidadMenu").val(), $("#isi_colorLetraMenu").val());
    coloresUsr($(this).val(), $("#isi_opacidadMenu").val(), $("#isi_colorLetraMenu").val());
});
// fin color de fondo

// opacidad
$("#isi_opacidadMenu").on("input", function(e) {
    colorMenuUsr($('#isi_colorMenu').val(), $(this).val(), $("#isi_colorLetraMenu").val());
    coloresUsr($('#isi_colorMenu').val(), $(this).val(), $("#isi_colorLetraMenu").val());
});
// fin opacidad

// color de letra
$("#isi_colorLetraMenu").on("input", function(e) {
    colorMenuUsr($('#isi_colorMenu').val(), $("#isi_opacidadMenu").val(), $(this).val());
    coloresUsr($('#isi_colorMenu').val(), $("#isi_opacidadMenu").val(), $(this).val());
});
// fin color de letra
// fin Menu personalizado
