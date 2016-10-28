<?php

namespace Isi\PersonalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IsiPersonalBundle:Default:index.html.twig');
    }
}
