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
            // var_dump ($error->getMessage());
            switch (true) {
                case stripos($error->getMessage(), "disable"): // User account is disabled.
                    $this->forward("isi_mensaje:msjFlash", array("id" => 41));
                    break;
                case stripos($error->getMessage(), "credential"): // Bad credentials.
                    $msjExtra = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 13))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 27, "msjExtra" => $msjExtra));
                    break;
                default:
                    $this->forward("isi_mensaje:msjFlash", array("id" => 4));
                    break;
            }
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

        $sesion->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoUsuarioA' aria-hidden='true'></i>&nbsp;<i class='fa fa-user fa-2x isi_iconoUsuarioA' aria-hidden='true'></i>");

        if (empty($request->getSession()->get("persSelecBD"))) {// si no busque las personas antes
            $this->forward("isi_mensaje:msjFlash", array("id" => 38));
            return $this->redirectToRoute("isi_persona_C");
        }
        else {
            // busco del listado en sesion cuales personas no tienen usuario y los guardo en sesion
            $usuarios = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->persSinUsuario($this->getUser()->getPerselec());
            if (count($usuarios) == 0) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 39));
                return $this->redirectToRoute("isi_sesion_homepage");
            }
            // cargo las imagenes para los usuarios
            $avatars = array();
            $directory = getcwd() . "/imagenes/avatar/";
            $dirint = dir($directory);
            while (($archivo = $dirint->read()) !== false)
                if( preg_match( "/png/i", $archivo ) )
                    array_push($avatars, substr ( $request->getUri() , 0, strpos($request->getUri(), "b/") + 2) . "imagenes/avatar/" . $archivo);
            $dirint->close();
            // fin cargo las imagenes para los usuarios

            if (empty($id)) // si no eligio una pesona, muestro la primera
                $usrSel = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->persSinUsuario($usuarios[0]->getId());
            else // sino el que eligio de la lista desplegable
                $usrSel = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->persSinUsuario($id);

            $usr = new Usuarios();
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Personas")->findOneById($usrSel[0]->getId());
            $usr->setPersona($resu);
            $usr->setUsername($resu->getEmail());
            $form = $this->createForm(UsuariosType::class, $usr);

            $form->handleRequest($request);
            if ($form->isValid()) {
                if (trim($resu->getEmail()) == "@") {
                    $this->forward("isi_mensaje:msjFlash", array("id" => 43));
                    return $this->redirectToRoute("isi_sesion_usrA");
                }
                if ($form->getData()->getPassword() === $form->get("password2")->getData()) {
                    if (empty($form->getData()->getImagen())) // si no eligio avatar, le asigno la imagen por defecto
                        $form->getData()->setImagen(base64_encode(file_get_contents($directory . "sin_avatar.png")));

                    try {
                        $form->getData()->setUsername($resu->getEmail());
                        $form->getData()->setEmail($resu->getEmail());
                        $form->getData()->setPersona($resu);
                        $form->getData()->setPassword(md5($form->getData()->getPassword()));
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($form->getData());
                        $em->flush();
                        $this->forward("isi_mensaje:msjFlash", array("id" => 36));
                        // return $this->redirectToRoute("isi_sesion_usrA");
                    }
                    catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                        $band = false;
                        $msjExtra = $e->getMessage();
                        $this->forward("isi_mensaje:msjFlash", array("id" => 44));
                        // $this->forward("isi_mensaje:msjFlash", array("id" => 9, "msjExtra" => $msjExtra));
                    }
                    catch (\Exception $e) {
                        $band = false;
                        $text = $e->getMessage();
                        $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar una persona</u> <br>" . $e->getMessage()));
                        return $this->redirectToRoute("isi_sesion_usrA");
                    }
                } else {
                    $this->forward("isi_mensaje:msjFlash", array("id" => 40));
                }
            }
        }
        return $this->render("IsiSesionBundle:Default:formulario.html.twig", array("form"=>$form->createView(), "usuarios" => $usuarios, "idSel" => $id, "avatars" => $avatars));
   }

   public function listadoAction(Request $request)
   {
       $request->getSession()->set("icoNombre", "<i class='fa fa-user fa-2x isi_iconoUsuarioL' aria-hidden='true'></i>");
       try {
           $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:EstCiviles")->findAllOrdByDescrip();
       } catch (\Exception $e) { // $e->getMessage()
           $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>index estado civil</u>")); // usando un servicio
           $resu = null;
       }
       return $this->render("IsiConfigBundle:EstadoCivil:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
   }

}
