$(document).ready(function(){
    // ************************* Código propocionado
    /* mdl-js-data-table mdl-data-table--selectable ya no será soportado por ser mas complejo acceder a los datos
    // que con un checkbox común. Código propocionado por doc oficial
    https://github.com/google/material-design-lite/wiki/Deprecations
    */
    // var $tabla = document.querySelector('table');
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

    $('#borrarRegs').click(function() {
        var $cant = 0;
        var notification = document.querySelector('.mdl-js-snackbar');
        var $error = false;
        $('#tLista').find('input[type="checkbox"]:checked').each(function (i, fila) {
            if (($.isNumeric(fila['id']))&&(!$error)) { // sin esto tambien cuenta el check de "(des)Tildar todos"
               $cant++;
                $.ajax({
                    url:'http://localhost/integra-si/web/app_dev.php/sistema/config/estCivil/borrar/' + fila['id'],
                    method:'POST',
                    beforeSend:function(xhr){
                        if ($cant == 1)
                            $("#msjPag").html("Eliminando....");
                    },
                    success:function(response, status, request){
                        $("#msjPag").html("");
                    },
                    error:function(xhr, textStatus, errorThrown){
                        $error = false;
                        $("#msjPag").html("error....");
                    }
                });
            }
        });

        // mostrar mensaje toast / snack
        $msj = "";
        switch (true) {
            case ($cant == 0) :
            $msj = "Elija en elemento para eliminarlo";
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
            , timeout: 5000 // msegs
            // , actionHandler: function(event) {}
            // , actionText: 'Deshacer'
        });
        // Fin mostrar mensaje toast / snack
    });
});
