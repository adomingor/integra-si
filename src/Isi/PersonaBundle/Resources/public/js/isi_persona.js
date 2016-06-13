$(document).ready(function() {
    $(".input-daterange").datepicker({
        format: "dd-mm-yyyy",
        todayBtn: true,
        language: "es",
        daysOfWeekHighlighted: "0,6",
        autoclose: true,
        todayHighlight: true,
        toggleActive: true
    });
    // focos a objetos
    $("#isi_btn_agregar").click(function(evento) {
        $("#lugar_nacim_descrip").focus();
    });
});
