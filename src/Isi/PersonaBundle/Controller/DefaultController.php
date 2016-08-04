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
        $request = Request::createFromGlobals();
        // tabla dnies
        $form->getData()->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        // tabla personas
        $form->getData()->personas->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->personas->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->personas->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        // tabla dnies
        $form->getData()->SetUsuarioActu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->SetIpActu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->SetFechaActu(new \DateTime()); // fecha y hora en que actualiza el registro
        // tabla personas
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
        catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) { // $e->getMessage()
            $band = false;
            $msjExtra = "<br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 28, "msjExtra" => $msjExtra));
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $text = $e->getMessage();
            switch (true) {
                case stristr($text, "chk_dnies_numero"): # violacion de check de dni válido (codigo de violacion de check 23514)
                    $this->forward("isi_mensaje:msjFlash", array("id" => 29));
                    break;
                default:
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar una persona</u> <br>" . $e->getMessage()));
                    break;
            }
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

    // // usado para controlar que no exista el dni al momento de dar de alta
    // public function ctrlAltaPersAction(Request $request, $numero)
    // {
    //     $request->getSession()->remove("icoNombre");
    //     try {
    //         $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Dnies")->findOneByNumero($numero);
    //         if ($resu) {
    //             $array = ["error" => "", "existe" => "true"];
    //         } else {
    //             $array = ["error" => "","existe" => "false"];
    //         }
    //     } catch (\Exception $e) { // $e->getMessage()
    //         $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>ctrl existe pesona</u>")); // usando un servicio
    //         $array = ["error" => $e->getMessage(),"resu" => "error"];
    //     }
    //     $resu = null;
    //     return new JsonResponse($array);
    // }


    // analiza si es consulta avanzada o plana
    private function tipoConsFTS($busqueda)
    {
        // si tiene & o ! o | es consulta avanzada
        switch (true) {
            case preg_match("/&|!|\|/", $busqueda):
                $tipoCons = "to_tsquery";
                break;
            default:
                $tipoCons = "plainto_tsquery"; // busqueda avanzada
                break;
        }
        return $tipoCons;
    }

    public function buscarPersAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-search fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>");
        $resu = null;
        // $pagination = null;
        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add("txtABuscar", TextType::class)
            ->add("chkCard", CheckboxType::class, array("required"=>false)) //si esta chequeado se muestra como recuadros, sino como listado
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $maxCant = 300;
                $resu = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonasFts($form->get("txtABuscar")->getdata(), $this->tipoConsFTS($form->get("txtABuscar")->getdata()), $maxCant);
            } catch (\Exception $e) {
                $text = $e->getMessage();
                // var_dump($text);
                switch (true) {
                    case stristr($text, "42601"): # error en sintaxis sql
                        $this->forward("isi_mensaje:msjFlash", array("id" => 31));
                        break;
                    case stristr($text, "SuperaMaximo"): # supera el maximo
                        $cant = strstr($text, ' '); // busca en el "error" un espacio (cuando es SuperaMaximo le paso la cantidad de registros devueltos)
                        $msjExtra = "<br>Se encontraron<span class='text-danger'>" . $cant . "</span> personas.<br>Se mostrarán como máximo <span class='text-success'>" . $maxCant . "</span><br><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 32))->getContent(), true)["descrip"];
                        $this->forward("isi_mensaje:msjFlash", array("id" => 33, "msjExtra" => $msjExtra));
                        break;
                    default:
                        $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando personas</u>"));
                        // $this->orward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>consultando personas</u> <br>" . $e->getMessage()));
                        break;
                }
                // var_dump($resu);
                $resu = null;
                return $this->redirectToRoute("isi_persona_buscarPers");
            }

            if (count($resu) == 0)
                $this->forward("isi_mensaje:msjFlash", array("id" => 6));


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
        return $this->render("IsiPersonaBundle:Default:buscarPersona.html.twig", array("form"=>$form->createView(), "listado" => $resu, "totRegi" => count($resu), "tipoVista" => $form->get("chkCard")->getdata()));
    }
}
