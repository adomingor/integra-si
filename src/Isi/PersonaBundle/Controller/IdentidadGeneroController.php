<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentidadGeneroController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "Género de personas con problemas de pelotudez humana");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->findAll();
        return $this->render("IsiPersonaBundle:IdentidadGenero:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

    /**
     * @Route("/genero/formulario")
     */
    public function formularioAction()
    {
        return $this->render('IsiPersonaBundle:IdentidadGenero:formulario.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/genero/edicion/{id}")
     */
    public function edicionAction($id)
    {
        return $this->render('IsiPersonaBundle:IdentidadGenero:formulario.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/genero/borrar/{id}")
     */
    public function borrarAction($id)
    {
        return $this->render('IsiPersonaBundle:IdentidadGenero:listado.html.twig', array(
            // ...
        ));
    }

}
