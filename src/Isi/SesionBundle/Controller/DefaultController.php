<?php

namespace Isi\SesionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Isi\SesionBundle\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
       // $request->getSession()->set('icoNombre', 'images/logo_empresa_simple.png');
       $request->getSession()->remove('icoNombre');
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
        // var_dump($request->getUri());

        // echo $_SERVER['PATH_INFO'];

        // echo $_SERVER['PATH_TRANSLATED'];


        // echo basename($_SERVER['REQUEST_URI']);
        // echo $_SERVER['HTTP_HOST'];
        // echo basename(__FILE__);
        // echo dirname(__FILE__);

        echo $_SERVER['PHP_SELF'];
        echo $_SERVER['REQUEST_URI'];


        if (!empty($usuario)) {
            try {
                $resu = $this->getDoctrine()->getRepository("IsiSesionBundle:Usuarios")->findUsrByName($usuario);
                if (empty($resu))
                    $resu = "";
                else
                {
                    // $image = 'myimage.png';
                    // $type = pathinfo($image, PATHINFO_EXTENSION);
                    // $data = file_get_contents($image);
                    // $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            } catch (\Exception $e) { // $e->getMessage()
                $resu = "";
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>Actualiza Avatar</u>"));
            }
        }
        return new JsonResponse($resu);
    }
}
