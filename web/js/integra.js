$(document).ready(function(){
    $("#checkTodos").click(function(evento) {
        if ($("#checkTodos").length) { // si existe en la pagina el checkbox de (des)Tildar todos
          // ************************* Código propocionado
          /* mdl-js-data-table mdl-data-table--selectable ya no será soportado por ser mas complejo acceder a los datos
          // que con un checkbox común. Código propocionado por doc oficial
          https://github.com/google/material-design-lite/wiki/Deprecations
          */
          var $tabla = document.querySelector('table');
          // var $headerCheckbox = $tabla.querySelector('thead .mdl-data-table__select input');
          var $headerCheckbox = $tabla.querySelector('thead .sin_clase_chk');
          var $boxes = $tabla.querySelectorAll('tbody .sin_clase_chk');
          var headerCheckHandler = function(event) {
            if (event.target.checked) {
              for (var i = 0, length = $boxes.length; i < length; i++) {
                $boxes[i].MaterialCheckbox.check();
            }
            } else {
              for (var i = 0, length = $boxes.length; i < length; i++) {
                $boxes[i].MaterialCheckbox.uncheck();
              }
            }
          };
          $headerCheckbox.addEventListener('change', headerCheckHandler);
          // ************************* Fin código propocionado
        }
    });

    $("#verChekTodos").click(function(evento) {
        $("#checkTodos").click;
        $("[name='verSiNo']").toggleClass("quitarElemento");
    });

    function crearAjax(){
        /* Declarando variable a retornar con nuestro objeto, retornaremos "false"
        * en caso de algún error
        */
        var objetoAjax = false;
        //Preguntando si nuestro querido usuario aún usa Internet Explorer
        if(navigator.appName=="Microsoft Internet Explorer")
            objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
        else //De lo contrario está usando otro navegador, por supuesto uno mejor
            objetoAjax = new XMLHttpRequest();
        return(objetoAjax); //Retornamos nuestro objeto
    }


    $("#borrarRegs").click(function(evento) {
        evento.preventDefault();
        var notification = document.querySelector('.mdl-js-snackbar');
        var $error = false;
        var $cant = $("input[name='chkMultiAccion']:checked").length;

        // verificamos que este visible la columna de selección múltiple
        if ($("[name='verSiNo']").hasClass("quitarElemento")) {
            notification.MaterialSnackbar.showSnackbar({
                message: "Active la opción de 'Selección múltiple'"
                , timeout: 2500 // msegs
                // , actionHandler: function(event) {/*funcion del boton*/}
                // , actionText: 'nombre de la accion '
            });
            return false;
        }


        var $objXhr = crearAjax(); // intentamos crear el objeto ajax
        if ($objXhr === false) {
            $("#msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al crear el objeto ajax. <br> Contacte a Informática!.");
            return false;
        }

        $("input[name='chkMultiAccion']:checked").each(function (i, fila) {
            // $url= $('#borrarRegs').attr('href') + '/' + fila['id'];
            $url= $("#borrarRegs").attr('href') + '/' + fila.value;
	        // $url='/direccion/falsa/3';
            $objXhr = $.ajax({
                url: $url,
                method:'POST',
                async: false, /* falso = sincronico = 1 petición a la vez*/
                beforeSend:function(xhr){
                    if (i == 0)
                        $("#msjProcesando").toggleClass("is-active");
                    $("#msjPag").html("<i class='material-icons mdl_menu__icono_centrado'>delete</i> (" + (i+1) + ")");
                },
                success:function(response, status, request){
                    $( "#msjProcesando" ).removeClass( "is-active" )
                    $("#msjPag").html("");
                },
                error:function(xhr, textStatus, errorThrown){
                    $("#msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                    $("#msjProcesando").toggleClass( "is-active" )
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
                // $("#tLista input[type=checkbox]").prop('checked', false); // destilda solo checkbox los del objeto #tLista
                $("input[name='chkMultiAccion']:checked").prop('checked', false); // destilda solo checkbox los del objeto #tLista
                setTimeout(function(){window.location.reload();},1500);
            }
        }
    });
});
