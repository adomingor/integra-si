<?php

namespace Isi\PersonaBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class F3Persona
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    private function analizaFts($busqueda)
    {
        if (!preg_match("/&|!|\|/", $busqueda)) { // si no escribió en formato ts_query
            // alberto    riviere,    molina eliana, -elizabeth - gonzalez,   26139712
            $busqueda = trim((preg_replace('/\s\s+/', ' ', $busqueda))); // 1 dejamos la cadena con 1 solo espacio entre palabras y le quitamos los iniciales y finales
            $busqueda = str_replace(" ,",",", $busqueda); // quito los espacios antes de las comas
            $busqueda = str_replace(", ", ",", $busqueda); // quito los espacios despues de las comas
            $busqueda = str_replace("- ", "-", $busqueda); // quito si hubiera espacios despues del -
            $busqueda = str_replace(",-", "&!", $busqueda); // reemplazo los ,- por &!
            $busqueda = str_replace(" ", "&", $busqueda); // reemplazo los espacios por &
            $busqueda = str_replace(",", "|", $busqueda); // reemplazo las , por |
        }
        $busqueda = str_replace("-", "!", $busqueda); // reemplazo los - que quedan por !
        return ($busqueda);
    }

    public function buscar(Request $request, $texto)
    {
        echo("<br>");
        echo("en el servicio");
        // caca;
        // try {
            $maxCant = 300;
            $resu = $resu = $this->em->getRepository("IsiPersonaBundle:Personas")->buscarPersonasFts($this->analizaFts($texto), $maxCant);
        // } catch (\Exception $e) {
        //     $text = $e->getMessage();
        //     switch (true) {
        //         case stristr($text, "42601"): # error en sintaxis sql
        //             $this->forward("isi_mensaje:msjFlash", array("id" => 31));
        //             // $this->forward("isi_mensaje:msjFlash", array("id" => 31, "msjExtra" => $text));
        //             break;
        //         case stristr($text, "SuperaMaximo"): # supera el maximo
        //             $cant = strstr($text, ' '); // busca en el "error" un espacio (cuando es SuperaMaximo le paso la cantidad de registros devueltos)
        //             $msjExtra = "<br>Se encontraron<span class='text-danger'>" . $cant . "</span> personas.<br>Se mostrarán como máximo <span class='text-success'>" . $maxCant . "</span><br><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 32))->getContent(), true)["descrip"];
        //             $this->forward("isi_mensaje:msjFlash", array("id" => 33, "msjExtra" => $msjExtra));
        //             break;
        //         default:
        //             $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando personas</u>"));
        //             // $this->orward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando personas</u> <br>" . $e->getMessage()));
        //             break;
        //     }
        //     // var_dump($resu);
        //     $resu = null;
        //     return $this->redirectToRoute("isi_persona_C");
        // }
        var_dump($resu);
        // caca;
        if (count($resu) == 0)
            $this->forward("isi_mensaje:msjFlash", array("id" => 6));
        else { // agregado por que muestro en la busqueda las pesonas en sesion y pueden estar repetidas en la busqueda
            $array = $request->getSession()->get("persSelecBD");
            if (!empty($array)) {
                $posi = array();
                foreach ($array as &$valor)
                    foreach ($resu as $key => $val)
                        if ($val['id'] === $valor["id"])
                            array_push($posi, $key);
                unset($valor); // rompe la referencia con el último elemento
                foreach ($posi as $valor) // elimina los repetidos
                    unset($resu[$valor]);
            }
        }
        return new JsonResponse($resu);
    }

    public function seleccionPersQuitar(Request $request)
    {
        $sesion = $request->getSession();
        $sesion->remove("persSelecBD");
        $sesion->remove("cantPerSel");
        $this->forward("isi_mensaje:msjFlash", array("id" => 37));
    }

    public function seleccionPersEnSesion(Request $request, $ids)
    {
        $sesion = $request->getSession();
        $resul = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonaXIds($ids);
        $this->getUser()->setPerselec($ids); // actualizo el objeto usuario de la sesión para poder usarlo en guardaSeleccionPersAction
        $sesion->set("persSelecBD", $resul);
        $sesion->set("cantPerSel", count($resul));
    }

    public function seleccionPersEnBD($ids) // grabo en la bd las personas para el usuario logueado
    {
        $usuario = $this->getDoctrine()->getRepository("IsiSesionBundle:Usuarios")->findOneByUsername($this->getUser()->getUsername());
        $usuario->setPerselec($ids);
        $this->getDoctrine()->getManager()->flush();
    }

    public function guardaSeleccionPersAction(Request $request, $idsCodi)
    {
        if (empty($idsCodi)) {
            $this->seleccionPersQuitar($request);
            $this->seleccionPersEnBD('');
        }
        else {
            // proceso para guardar o agregar ids sin repetidos
            $arrCodi = array_filter(explode( '¬', $idsCodi)); // paso a array los ids codificados, estan separados por el caracter ¬
            foreach ($arrCodi as &$valor) { $valor = $this->get('nzo_url_encryptor')->decrypt($valor); } // decodifico los ids
            unset($valor); // rompe la referencia con el último elemento
            $arrUnidos = array_merge($arrCodi, array_filter(explode( ',', $this->getUser()->getPerselec()))); // uno el array nuevo con el array (lo convierto primero) del usuario si lo tuviera
            $ids = implode(",", array_unique($arrUnidos)); // elimino los repetidos y convierto el array en cadena separada por comas
            // fin proceso para guardar o agregar ids sin repetidos
            try {
                $this->seleccionPersEnSesion($request, $ids);
                $this->seleccionPersEnBD($ids);
            } catch (Exception $e) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u>"));
                // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u> <br>" . $e->getMessage()));
                // echo ($e->getMessage());
            }
        }
        return $this->redirectToRoute('isi_persona_C');
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function eliminarUnaPersSeleccionAction(Request $request, $id)
    {
        $idsUsr = array_filter(explode( ',', $this->getUser()->getPerselec())); // obtengo las personas (del usuario), lo paso a array
        unset($idsUsr[array_search($id, $idsUsr)]); // busco el id, y lo quito del array
        $this->seleccionPersEnBD(implode(",", $idsUsr)); // actualizo los datos de la sesion del usuario
        foreach ($idsUsr as &$valor) { $valor = $this->get('nzo_url_encryptor')->encrypt($valor); } // codifico los ids
        return $this->redirectToRoute('isi_persona_ABMSelPers', array('idsCodi' => implode("¬", $idsUsr)));
    }


    // public function buscar(Request $request, $texto) {
    //     try {
    //         $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
    //         if ($resu) {
    //             $array = [
    //                 "tipo" => $resu[0]["tipoMensaje"]["descrip"],
    //                 "titulo" => $resu[0]["titulo"],
    //                 "descrip" => $resu[0]["descrip"] . $msjExtra,
    //             ];
    //         } else {
    //             $array = [
    //                 "tipo" => "warning",
    //                 "titulo" => "no existe el " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>",
    //                 "descrip" => "",
    //             ];
    //         }
    //     } catch (\Exception $e) {
    //         $array =[
    //             "tipo" => "error",
    //             "titulo" => "consultando BD",
    //             "descrip" => $e->getMessage(),
    //         ];
    //     }
    //     return new JsonResponse($array);
    // }

}
