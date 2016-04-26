<?php

namespace Isi\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IsiConfigBundle:Default:index.html.twig');
    }
}
