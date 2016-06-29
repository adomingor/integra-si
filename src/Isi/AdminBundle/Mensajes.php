<?php

namespace Isi\AdminBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Mensajes
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function msjFlash(Request $request, $id) {
        $tipo = "warning";
        $titulo = "no existe el " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>";
        $descrip = "";
        try {
            $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
            // esto si el repositorio devuelve array
            $tipo = $resu[0]["tipoMensaje"]["descrip"];
            $titulo = $resu[0]["titulo"];
            $descrip = $resu[0]["descrip"];
            // esto si el repositorio devuelve objeto
            // $tipo = $resu[0]->getTipoMensaje()->getDescrip();
            // $titulo = $resu[0]->getTitulo();
            // $descrip = $resu[0]->getDescrip();
        } catch (\Exception $e) {
            $tipo = "error";
            $titulo = "consultando BD";
            $descrip = $e->getMessage();
        }
        return new Response($request->getSession()->getFlashBag()->add($tipo, $titulo . "¬ " . $descrip));
    }

    public function msjArray(Request $request, $id) {
        $array = [
            "tipo" => "warning",
            "titulo" => "no existe el " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>",
            "descrip" => "",
        ];
        try {
            $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
            $array =[
                "tipo" => $resu[0]["tipoMensaje"]["descrip"],
                "titulo" => $resu[0]["titulo"],
                "descrip" => $resu[0]["descrip"],
            ];

        } catch (\Exception $e) {
            $array =[
                "tipo" => "error",
                "titulo" => "consultando BD",
                "descrip" => $e->getMessage(),
            ];
        }
        // var_dump($array);
        // var_dump(json_encode($array));
        // return new Response($array);
        return new JsonResponse($array);
        // $response = new JsonResponse();
        // $response->setData($array);
        // return $response;


        // return new Response(json_encode($array));
    }
}
