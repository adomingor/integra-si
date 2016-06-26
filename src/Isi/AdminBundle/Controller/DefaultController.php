<?php

namespace Isi\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IsiAdminBundle:Default:index.html.twig');
    }
}
