var $isi_tiemMsj = 10000; // tiempo q se muestra el mensaje flash de controladores antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjMedio = 6500 // tiempo q se muestra el mensaje flash secundarios antes de desaparecer (usado con sweetAlert2)
var $isi_tiemMsjCorto = 4000; // tiempo q se muestra el mensaje flash para confirmaciones antes de desaparecer (usado con sweetAlert2)
var $isi_tiemRecarga = 1700; // tiempo q se muestra el mensaje flash antes de recargar la pagina (usado con sweetAlert2)
var $isi_tiemRecargaCorto = 200; // tiempo para recargar la pagina así no muestra dos veces el mensaje sweetAlert2 (usado con sweetAlert2)
var $ayuda = '<br><p class="text-muted"><small><i class="fa fa-lightbulb-o fa-lg text-info" aria-hidden="true"></i> utiliza la opción de búsqueda de personas para verificar existe.</small></p>';
// control para casilla nn (esta contemplada en la de abajo si no uso esta)

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
