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
        $maxCant = 300;
        try {
            $resu = $this->em->getRepository("IsiPersonaBundle:Personas")->buscarPersonasFts($this->analizaFts($texto), $maxCant);
            if (count($resu) > 0) { // agregado por que muestro en la busqueda las pesonas en sesion y pueden estar repetidas en la busqueda
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
        } catch (\Exception $e) {
            $text = $e->getMessage();
            switch (true) {
                case stristr($text, "42601"): # error en sintaxis sql
                    try {
                        $msjBD = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById(31);
                        $request->getSession()->getFlashBag()->add($msjBD[0]["tipoMensaje"]["descrip"], '¬' . $msjBD[0]["descrip"]);
                    } catch (\Exception $e) { // en realidad no pudo conectarse a la bd para mostrar el mensaje anterior, pero ya viene de un error anterior
                        $request->getSession()->getFlashBag()->add("warning", '¬No pude realizar la búsqueda, hay alguna letra o palabra que no entiendo.');
                    }
                    break;
                case stristr($text, "SuperaMaximo"): # supera el maximo
                    $cant = strstr($text, ' '); // busca en el "error" un espacio (cuando es SuperaMaximo le paso la cantidad de registros devueltos)
                    $msjExtra = "<br>Se encontraron<span class='text-danger'>" . $cant . "</span> personas.<br>Se mostrarán como máximo <span class='text-success'>" . $maxCant . "</span>";
                    try {
                        $msjBD = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById(32);
                        $msjBD2 = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById(33);
                        $msjExtra = $msjExtra . $msjBD[0]["descrip"];
                        $request->getSession()->getFlashBag()->add($msjBD[0]["tipoMensaje"]["descrip"], '¬' . $msjBD2[0]["descrip"] . $msjExtra);
                    } catch (\Exception $e) { // en realidad no pudo conectarse a la bd para mostrar el mensaje anterior, pero ya viene de un error anterior
                        $request->getSession()->getFlashBag()->add("warning¬", $msjExtra);
                    }
                    break;
                default:
                    $msjExtra = "<br> <u class='text-danger'>consultando personas</u>";
                    try {
                        $msjBD = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById(1);
                        $request->getSession()->getFlashBag()->add($msjBD[0]["tipoMensaje"]["descrip"], '¬' . $msjBD[0]["descrip"] . $msjExtra);
                    } catch (\Exception $e) { // en realidad no pudo conectarse a la bd para mostrar el mensaje anterior, pero ya viene de un error anterior
                        $request->getSession()->getFlashBag()->add("error¬", $msjExtra);
                        // $request->getSession()->getFlashBag()->add("error¬", $msjExtra . "<br>" . $e->getMessage());
                    }
                    break;
            }
            $resu = null;
        }
        return new JsonResponse($resu);
    }
}
