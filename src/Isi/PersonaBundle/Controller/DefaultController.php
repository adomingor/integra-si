<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\Dnies;
use Isi\PersonaBundle\Form\DniesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DefaultController extends Controller
{
    private function usrCrea($form)
    {
        // var_dump($form->getData());
        $request = Request::createFromGlobals();
        $form->getData()->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        $form->getData()->personas->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->personas->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->personas->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        $form->getData()->SetUsuarioActu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->SetIpActu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->SetFechaActu(new \DateTime()); // fecha y hora en que actualiza el registro
        $form->getData()->personas->SetUsuarioActu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->personas->SetIpActu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->personas->SetFechaActu(new \DateTime()); // fecha y hora en que actualiza el registro
        return($form);
    }

    private function grabar($form)
    {
        $band = true;
        try {
            $this->usrCrea($form); // datos del usuario q crea el registro
            $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar una persona</u> <br>" . $e->getMessage()));
        }
        return ($band);
    }

    public function nuevaAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoPersona' aria-hidden='true'></i>");
        $form = $this->createForm(DniesType::class, new Dnies());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form)) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 5));
                return $this->redirectToRoute("isi_persona_nueva");
            }
        }
        return $this->render("IsiPersonaBundle:Default:formulario.html.twig", array("form"=>$form->createView()));
    }

    // usado para controlar que no exista el dni al momento de dar de alta
    public function ctrlAltaPersAction(Request $request, $numero)
    {
        $request->getSession()->remove("icoNombre");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Dnies")->findOneByNumero($numero);
            if ($resu) {
                $array = ["error" => "", "existe" => "true"];
            } else {
                $array = ["error" => "","existe" => "false"];
            }
        } catch (\Exception $e) { // $e->getMessage()
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>ctrl existe pesona</u>")); // usando un servicio
            $array = ["error" => $e->getMessage(),"resu" => "error"];
        }
        $resu = null;
        return new JsonResponse($array);
    }

    public function buscarPersAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-search fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>");
        // $pagination = null;
        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add("txtABuscar", TextType::class)
            ->add("chkThumbs", CheckboxType::class, array('required'=>false)) //si esta chequeado se muestra como recuadros, sino como listado
            ->add("chkAvzada", CheckboxType::class, array('required'=>false)) // si esta chequeado se utiliza la busqueda avanzada del fts
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            // // $em = $this->get('doctrine')->getManager(); // <-- metodo original
            // $em = $this->getDoctrine()->getManager(); // <-- metodo abreviado
            // $resuBD = $em->getRepository('IsiPersonaBundle:Fts')->consultarFtsPersona($form->get('txtABuscar')->getdata(), $form->get('chkAvzada')->getdata(), $request->query->get("sort"), $request->query->get("direction"));
            //
            // if (array_key_exists('ups_Error', $resuBD)) {
            //     if ($resuBD['ups_Error']->getCode() == -69) // código personalizado al lanzar la excepción
            //         $this->addFlash('warning', $resuBD['ups_Error']->getMessage());
            //     else
            //         if ($form->get('chkAvzada')->getdata() == 1)
            //             $this->addFlash('danger', 'Probablemente la consulta avanzada esté mal escrita. Consulta la ayuda.<br>Si crees que es correcta, por favor contacta al administrador del sistema');
            //         else
            //             $this->addFlash('danger', 'Ocurrió un problema al intentar obtener los resultados.<br>Por favor contacta al administrador del sistema');
            // } else {
            //     $this->addFlash('warning', count($resuBD). ' coincidencias para: "'.$form->get('txtABuscar')->getdata().'"');
            //
            //     $request->getSession()->set('ParaExportar', $resuBD);
            //
            //     $paginator  = $this->get('knp_paginator');
            //     $pagination = $paginator->paginate(
            //         $resuBD,
            //         $request->query->get('page', 1), // número de página
            //         10 // cantidad de registros a mostrar
            //     );
            // }
        }
        return $this->render('IsiPersonaBundle:Default:buscarPersona.html.twig', array('form'=>$form->createView(), 'listado' => null));
    }
}
