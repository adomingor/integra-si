var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';

$("#isi_btn_f3SelPersLugTrab").click(function(elemento) {
    elemento.preventDefault();
    var $chks = $("input:checkbox:checked:not(:disabled):not(.isi_chk_grupo)[name=" + this.name + "]");
    lv_ids = [];
    $.each($($chks), function (indice, elemento) {
        lv_ids.push(elemento.value.trim());
    });

    alert("vamos por el buen camino.\n" + lv_ids);
});
