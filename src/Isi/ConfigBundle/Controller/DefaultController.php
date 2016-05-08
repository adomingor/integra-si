<?php

namespace Isi\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set('icoNombre', 'Configuración');
        return $this->render('IsiConfigBundle:Default:index.html.twig');
    }
}
