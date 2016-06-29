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
        try {
            $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
            if ($resu) {
                // esto si el repositorio devuelve array
                $tipo = $resu[0]["tipoMensaje"]["descrip"];
                $titulo = $resu[0]["titulo"];
                $descrip = $resu[0]["descrip"];
                // esto si el repositorio devuelve objeto
                // $tipo = $resu[0]->getTipoMensaje()->getDescrip();
                // $titulo = $resu[0]->getTitulo();
                // $descrip = $resu[0]->getDescrip();
            } else {
                $tipo = "warning";
                $titulo = "no existe el " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>";
                $descrip = "";
            }
        } catch (\Exception $e) {
            $tipo = "error";
            $titulo = "consultando BD";
            $descrip = $e->getMessage();
        }
        return new Response($request->getSession()->getFlashBag()->add($tipo, $titulo . "¬ " . $descrip));
    }

    public function msjJson(Request $request, $id) {
        try {
            $resu = $this->em->getRepository("IsiAdminBundle:Mensajes")->findMsById($id);
            if ($resu) {
                $array = [
                    "tipo" => $resu[0]["tipoMensaje"]["descrip"],
                    "titulo" => $resu[0]["titulo"],
                    "descrip" => $resu[0]["descrip"],
                ];
            } else {
                $array = [
                    "tipo" => "warning",
                    "titulo" => "no existe el " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>",
                    "descrip" => "",
                ];
            }        
        } catch (\Exception $e) {
            $array =[
                "tipo" => "error",
                "titulo" => "consultando BD",
                "descrip" => $e->getMessage(),
            ];
        }
        return new JsonResponse($array);
    }
}
