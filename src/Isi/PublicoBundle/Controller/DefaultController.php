<?php

namespace Isi\PublicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->remove('icoNombre');
        return $this->render('IsiPublicoBundle:Default:index.html.twig');
    }
}
