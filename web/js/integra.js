$(document).ready(function(){
    
    function tildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (index, element) {
            element.MaterialCheckbox.check();
        });
    }

    function desTildarMultiCheck($nombre) {
        $.each($("label[name='"+$nombre+"']"), function (index, element) {
            element.MaterialCheckbox.uncheck();
        });
    }

    $("#isi_checkTodos").click(function(evento) {
        if ($("#isi_checkTodos").length) { // si existe en la pagina el checkbox de (des)Tildar todos
            if (this.checked)
                tildarMultiCheck("isi_chkMultiAccion");
            else
                desTildarMultiCheck("isi_chkMultiAccion"); // destildo resto multicheck
        }
    });

    $("#isi_verChekTodos").click(function(evento) {
        desTildarMultiCheck("isi_checkTodos"); // destildo cabecera
        desTildarMultiCheck("isi_chkMultiAccion"); // destildo resto multicheck
        $("[name='isi_verSiNo']").toggleClass("isi_quitarElemento");
        if ($("[name='isi_verSiNo']").hasClass("isi_quitarElemento"))
            $("#isi_verChekTodos").html("Mostrar multicheck");
        else
            $("#isi_verChekTodos").html("Ocultar multicheck");
    });

    function crearAjax(){
        /* Declarando variable a retornar con nuestro objeto, retornaremos "false" * en caso de algún error */
        var objetoAjax = false;
        //Preguntando si nuestro querido usuario aún usa Internet Explorer
        if(navigator.appName=="Microsoft Internet Explorer")
            objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
        else //De lo contrario está usando otro navegador, por supuesto uno mejor
            objetoAjax = new XMLHttpRequest();
        return(objetoAjax); //Retornamos nuestro objeto
    }

    $("#isi_borrarRegs").click(function(evento) {
        evento.preventDefault();
        var notification = document.querySelector('.mdl-js-snackbar');
        var $error = false;
        var $cant = $("input[name='isi_chkMultiAccion']:checked").length;

        // verificamos que este visible la columna de selección múltiple
        if ($("[name='isi_verSiNo']").hasClass("isi_quitarElemento")) {
            notification.MaterialSnackbar.showSnackbar({
                message: "Active la opción 'multicheck'"
                , timeout: 2500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
            return false;
        }

        var $objXhr = crearAjax(); // intentamos crear el objeto ajax
        if ($objXhr === false) {
            $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al crear el objeto ajax. <br> Contacte a Informática!.");
            return false;
        }

        $("input[name='isi_chkMultiAccion']:checked").each(function (i, fila) {
            $url= $("#isi_borrarRegs").attr('href') + '/' + fila.value;
	        // $url='/direccion/falsa/3';
            $objXhr = $.ajax({
                url: $url,
                method:'POST',
                async: false, /* falso = sincronico = 1 petición a la vez*/
                beforeSend:function(xhr){
                    if (i == 0)
                        $("#isi_msjProcesando").removeClass("isi_quitarElemento");
                    $("#isi_msjPag").html("<br><div class='material-icons mdl-badge mdl-badge--overlap' data-badge=" + (i+1) + ">delete</div>");
                },
                success:function(response, status, request){
                    if ((i+1) == $cant) {
                        $("#isi_msjProcesando").addClass("isi_quitarElemento");
                        $("#isi_msjPag").html("");
                    }
                },
                error:function(xhr, textStatus, errorThrown){
                    $("#isi_msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                    $("#isi_msjProcesando").addClass("isi_quitarElemento");
                    return false;
                }
            });
        });

        if (($.inArray ($objXhr.status, [0, 200]) !== -1)) {
			// mostrar mensaje toast / snack si no hubo error
            $msj = "";
            switch (true) {
                case ($cant == 0) :
                $msj = "Elija algún elemento para eliminarlo";
                break;
                case ($cant == 1) :
                    $msj = "Se eliminó 1 registro";
                    break;
                case ($cant > 1) :
                    $msj = "Se eliminaron " + $cant + " registros";
                    break;
            };
            notification.MaterialSnackbar.showSnackbar({
                message: $msj
                , timeout: 1500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
			// Fin mostrar mensaje toast / snack si no hubo error

            if ($cant > 0) {
                $("input[name='isi_chkMultiAccion']:checked").prop('checked', false); // destilda solo checkbox los del objeto #tLista
                setTimeout(function(){window.location.reload();},1500);
            }
        }
    });
	
});