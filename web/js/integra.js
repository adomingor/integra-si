$(document).ready(function(){
    if ( $("#checkTodos").length ) { // si existe en la pagina el checkbox de (des)Tildar todos
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

    $('#borrarRegs').click(function(evento) {
        evento.preventDefault();
        var notification = document.querySelector('.mdl-js-snackbar');
        var $cant = 0;
        var $error = false;
        $('#tLista').find('input[type="checkbox"]:checked').each(function (i, fila) {
            if (($.isNumeric(fila['id']))&&(!$error)) { // sin esto tambien cuenta el check de "(des)Tildar todos"
                $cant++;
                $url= $('#borrarRegs').attr('href') + '/' + fila['id'];
		//$url='/direccion/falsa/3';
                $.ajax({
                    url: $url,
                    method:'POST',
                    async: false, /* false (1 peticion a la vez) no da error pero no muestra los mensajes flash, true, muestra los mensajes, pero da error*/
                    beforeSend:function(xhr){
                        if ($cant == 1)
                            $("#msjProcesando").toggleClass("is-active");
                        $("#msjPag").html("<i class='material-icons'>delete</i> (" + $cant + ")");
                    },
                    success:function(response, status, request){
                        $( "#msjProcesando" ).removeClass( "is-active" )
                        $("#msjPag").html("");
                    },
                    error:function(xhr, textStatus, errorThrown){
                        $error = true;
                        $("#msjPag").html("<i class='material-icons'>bug_report</i> Ups! ocurrió un error al intentar eliminar (" + errorThrown + ")");
                        $( "#msjProcesando" ).toggleClass( "is-active" )
                    }
                });
            }
        });

        if ((!$error)) {
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
                $("#tLista input[type=checkbox]").prop('checked', false); // destilda solo checkbox los del objeto #tLista
                setTimeout(function(){window.location.reload();},1500);
            }
        } 
    });
});
