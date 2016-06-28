<?php
namespace Isi\AdminBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Mensajes
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function msjFlash(Request $request ,$id) {
        $tipo = "warning";
        $titulo = "no se obtuvo " . "<i class='fa fa-commenting-o' aria-hidden='true' style='font-size:2em;'></i>";
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
            $resu = null;
        }
        return new Response($request->getSession()->getFlashBag()->add($tipo, $titulo . "¬ " . $descrip));
    }
}
