<?php

namespace Isi\SesionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Isi\SesionBundle\Entity\Usuarios;

//use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        // $request->getSession()->set('iconoPagina', 'images/logo_empresa_simple.png');
        $request->getSession()->remove('iconoPagina');
        return $this->render('IsiSesionBundle:Default:index.html.twig');
        //$this->getRequest()->setLocale('es_AR');
        //$translated = $this->get('translator')->trans('Bad credentials');
        //return new Response($translated);
    }

    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $sesion = $request->getSession();

        $usr = new Usuarios();
        $usr->setUsername($lastUsername);
        $form = $this->createFormBuilder($usr)
            ->setAction($this->generateUrl('login_check'))
            ->add('username')
            ->add('password', PasswordType::class)
            ->getForm();

        if ($error != null) {
            $this->addFlash('Orange-700', '¿Olvidaste tu contraseña?.');
        }

        return $this->render('IsiSesionBundle:Default:login.html.twig',
            array(
            'form'=>$form->createView(),
            'error'=> $error,
            )
        );
    }
}
