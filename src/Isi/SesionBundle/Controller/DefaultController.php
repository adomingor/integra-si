<?php

namespace Isi\SesionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Isi\SesionBundle\Entity\Usuarios;
use Isi\SesionBundle\Form\UsuariosType;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
       // $request->getSession()->set('icoNombre', 'images/logo_empresa_simple.png');
       $request->getSession()->remove('icoNombre');

       // si no hay variable en sesion, traigo los datos de la bd
       $sesion = $request->getSession();
       if (empty($sesion->get("persSelecBD"))) {
           //hacer un try, y ver 1ro si tiene datos de personas seleccionadas el usuario
           $pers = $this->getUser()->getPerselec();
           if (!empty($pers)) {
               $resul = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonaXIds($pers);
               $sesion->set("persSelecBD", $resul);
               $sesion->set("cantPerSel", count($resul));
           }
       }
       // fin si no hay variable en sesion, traigo los datos de la bd
       return $this->render('IsiSesionBundle:Default:index.html.twig');
       //$this->getRequest()->setLocale('es_AR');
       //$translated = $this->get('translator')->trans('Bad credentials');
       //return new Response($translated);
   }

    public function loginAction(Request $request)
    {
        $request->getSession()->remove('icoNombre');
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
            $msjExtra = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 13))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 27, "msjExtra" => $msjExtra));
        }

        return $this->render('IsiSesionBundle:Default:login.html.twig',
            array(
            'form'=>$form->createView(),
            'error'=> $error,
            )
        );
    }

    public function actuAvatarAction($usuario, Request $request)
    {
        $request->getSession()->remove('icoNombre');
        $resu = "";
        if (!empty($usuario)) {
            try {
                $resu = $this->getDoctrine()->getRepository("IsiSesionBundle:Usuarios")->findUsrByName($usuario);
                if (empty($resu))
                    $resu = "";
            } catch (\Exception $e) { // $e->getMessage()
                $resu = "";
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>Actualiza Avatar</u>"));
            }
        }
        return new JsonResponse($resu);
    }

    public function nuevoAction(Request $request, $id)
    {
        $sesion = $request->getSession();

        $sesion->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoUsuario' aria-hidden='true'></i>&nbsp;<i class='fa fa-user fa-2x isi_iconoUsuario' aria-hidden='true'></i>");

        if (empty($request->getSession()->get("persSelecBD"))) {// si no busque las personas antes
            $this->forward("isi_mensaje:msjFlash", array("id" => 38));
            return $this->redirectToRoute("isi_persona_C");
        }
        else {
            // cargo las imagenes para los usuarios
            $avatars = array();
            $directory = getcwd() . "/imagenes/avatar/";
            $dirint = dir($directory);
            while (($archivo = $dirint->read()) !== false)
            {
                if (eregi("png", $archivo)) {
                    array_push($avatars, substr ( $request->getUri() , 0, strpos($request->getUri(), "b/") + 2) . "imagenes/avatar/" . $archivo);
                }
            }
            $dirint->close();
            // var_dump($avatars);
            // fin cargo las imagenes para los usuarios

            // busco del listado en sesion cuales personas no tienen usuario y los guardo en sesion
            if (empty($id))
                $resu = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->persSinUsuario($this->getUser()->getPerselec());
            else
                $resu = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->persSinUsuario($id);
            //fin busco del listado en sesion cuales personas no tienen usuario y los guardo en sesion
            if (count($resu) == 0) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 38));
                return $this->redirectToRoute("isi_sesion_homepage");
            }
            // var_dump($resu[0]->getId());
            $usr = new Usuarios();
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Personas")->findOneById($resu[0]->getId());
            $usr->setPersona($resu);
            $form = $this->createForm(UsuariosType::class, $usr);

            // $form = $this->createForm(UsuariosType::class, new Usuarios());
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 5));
                // if ($this->grabar($form)) {
                //     $this->forward("isi_mensaje:msjFlash", array("id" => 36));
                //     return $this->redirectToRoute("isi_sesion_usrA");
                // }
            }
        }
        return $this->render("IsiSesionBundle:Default:formulario.html.twig", array("form"=>$form->createView(), "usuarios" => $resu, "idSel" => $id, "avatars" => $avatars));
   }
}
