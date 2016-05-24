<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class IdentGenerosController extends Controller
{
    /**
     * @Route("/formulario")
     */
    public function formularioAction()
    {
        return $this->render('IsiPersonaBundle:IdentGeneros:formulario.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edicion")
     */
    public function edicionAction()
    {
        return $this->render('IsiPersonaBundle:IdentGeneros:formulario.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/borrar")
     */
    public function borrarAction()
    {
        return $this->render('IsiPersonaBundle:IdentGeneros:listado.html.twig', array(
            // ...
        ));
    }

}
