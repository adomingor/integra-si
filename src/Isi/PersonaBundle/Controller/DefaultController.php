<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
// use Isi\PersonaBundle\Entity\Personas;
// use Isi\PersonaBundle\Form\PersonasType;
use Isi\PersonaBundle\Entity\Dnies;
use Isi\PersonaBundle\Form\DniesType;

class DefaultController extends Controller
{
    private function usrCrea($form)
    {
        $request = Request::createFromGlobals();
        $form->getData()->SetUsuarioCrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->SetIpCrea($request->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->SetFechaCrea(new \DateTime()); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        $form->getData()->SetUsuarioActu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->SetIpActu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->SetFechaActu(new \DateTime()); // fecha y hora en que actualiza el registro
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
        catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            $band = false;
            $msjExtra = "Ya existe el género <b class='text-warning'>".$form->getData()->getGenero() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar el género</u>"));
            // $this->addFlash("error", "Ups! ¬" . $e->getMessage());
        }
        return ($band);
    }

    public function nuevaAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-users fa-2x isi_iconoPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg isi_iconoPersona' aria-hidden='true'></i>");
        $estCivil = new Dnies();
        $form = $this->createForm(DniesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // if ($this->grabar($form))
                $this->addFlash("success", "Se agregó la persona");
            // return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        return $this->render("IsiPersonaBundle:Default:formulario.html.twig", array("form"=>$form->createView(), "idForm"=>"fPersNueva", "urlAction"=>$request->getUri()));
        // return $this->render("IsiPersonaBundle:Default:formulario.html.twig", array("form"=>$form->createView()));
    }

    // // public function edicionAction(Request $request, $id)
    // // {
    // //     $request->getSession()->set("icoNombre", "Edición de Estado Civil");
    // //     $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
    // //     if (!$resu){
    // //         $this->addFlash("Red-700", "No existe el estado civil que quiere editar");
    // //         return $this->redirectToRoute("isi_persona_estadoCivil");
    // //     } else {
    // //         $desc = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
    // //         $codi = $resu->getCodindec(); // guardo solo para mostrar lo que se modifico
    // //         $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
    // //         $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
    // //         $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
    // //         $form = $this->createForm(EstCivilesType::class, $resu);
    // //         $form->handleRequest($request);
    // //         if ($form->isValid()) {
    // //             // controlo q no exista el código del Indec si es mayor que 0
    // //             $band = false;
    // //             if ($form->getData()->getCodindec() > 0) {
    // //                 $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
    // //                 if (($cons)&&($resu->getId() != $cons[0]->getId()) ) {
    // //                     $band = true;
    // //                     $this->addFlash("Orange-700", "Ya existe el código del indec: '".$cons[0]->getCodindec()."' en: '".$cons[0]->getDescrip()."'!" );
    // //                 }
    // //             }
    // //             // Fin controlo q no exista el código del Indec si es mayor que 0
    // //             if (!$band) {
    // //                 try {
    // //                     // el usuario creador no se modifica
    // //                     $form->getData()->SetUsuarioCrea($usrCrea);
    // //                     $form->getData()->SetIpCrea($ipCrea);
    // //                     $form->getData()->SetFechaCrea($fechaCrea);
    // //                     $this->usrActu($form); // datos del usuario q actualiza el registro
    // //                     $this->getDoctrine()->getManager()->flush();
    // //                     $this->addFlash("Green-700", "Se modificó '".$desc."(".$codi.")'"." por '".trim($form->getData()->getDescrip())." (".$form->getData()->getCodindec().")'.");
    // //                 }
    // //                 catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
    // //                     $band = false;
    // //                     $this->addFlash("Red-900", "Ya existe el estado civil por el que intenta cambiar");
    // //                 }
    // //                 catch (\Exception $e) { // excepcion general
    // //                     $band = false;
    // //                     $this->addFlash("Red-900", "Ups!: ".$e->getMessage());
    // //                 }
    // //             }
    // //             return $this->redirectToRoute('isi_persona_estadoCivil');
    // //         }
    // //         return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivActu", "urlAction"=>$request->getUri()));
    // //     }
    // // }
}
