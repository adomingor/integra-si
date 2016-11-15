var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';

$("#isi_btn_f3SelPersLugTrab").click(function(elemento) {
    elemento.preventDefault();
    // name="lst_resu_pers"
    var $chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=lst_resu_pers]");
    lc_ids = "";
    var band = true;
    $.each($($chks), function (indice, elemento) {
        if (band)
            lc_ids = elemento.value.trim();
        else
            lc_ids = lc_ids + "¬" + elemento.value.trim();
        band = false;
    });

    alert("vamos por el buen camino.\n" + lc_ids);
});
