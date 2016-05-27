<?php
namespace Isi\PersonaBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\EstCiviles;
use Isi\PersonaBundle\Form\EstCivilesType;
use Symfony\Component\HttpFoundation\Response;

class EstadoCivilController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "Estado Civil");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findAllOrdByDescrip();
        return $this->render("IsiPersonaBundle:EstadoCivil:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

    private function usrCrea($form)
    {
        // $form->getData()->SetUsuariocrea($this->getUser()->getUsername()); // usuario q crea el registro
        // $form->getData()->SetIpcrea($request->getClientIp()); // ip del usaurio q crea el registro
        // $form->getData()->SetFechacrea( new \DateTime() ); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        // $form->getData()->SetUsuarioactu($this->getUser()->getUsername()); // usuario q actualiza el registro
        // $form->getData()->SetIpactu($request->getClientIp()); // ip del usaurio q actualiza el registro
        // $form->getData()->SetFechaactu( new \DateTime() ); // fecha y hora en que actualiza el registro
        return($form);
    }

    private function grabar($form)
    {
        $band = true;
        if ($form->getData()->getCodindec() > 0) {
            $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
            if ($cons) {
                $band = false;
                $this->addFlash("Orange-700", "Ya existe el código del indec: '" . $cons[0]->getCodindec() . "' en: '" . $cons[0]->getDescrip() . "' !'");
            }
        }
        else {
            if ($form->getData()->getCodindec() < 0) {
                $band = false;
                $this->addFlash("Orange-700", "El código del Indec no es válido!");
            }
        }
        if ($band) {
            try {
                $this->usrCrea($form); // datos del usuario q crea el registro
                $this->usrActu($form); // datos del usuario q actualiza el registro, cuando se crea el registro, es el mismo
                $em = $this->getDoctrine()->getManager();
                $em->persist($form->getData());
                $em->flush();
            }
            catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $band = false;
                // $this->addFlash('Orange-700', 'Ups! Ésto ocurrió "' . $e->getMessage());
                $this->addFlash("Red-900", "Ya existe el estado civil que intenta agregar");
            }
            catch (\Exception $e) { // excepcion general
                $band = false;
                $this->addFlash("Red-900", "Ups!: " . $e->getMessage());
            }
        }

        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "Estado Civil Nuevo");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("Green-700", "Se agregó '". trim($form->getData()->getDescrip()) ."' (".$form->getData()->getCodindec().").'");
            return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivNuevo", "urlAction"=>$request->getUri()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "Edición de Estado Civil");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        if (!$resu){
            $this->addFlash("Red-700", "No existe el estado civil que quiere editar");
            return $this->redirectToRoute("isi_persona_estadoCivil");
        } else {
            $desc = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
            $codi = $resu->getCodindec(); // guardo solo para mostrar lo que se modifico
            $form = $this->createForm(EstCivilesType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                // controlo q no exista el código del Indec o la descripción por el que se modifica
                $idRegi = null;
                $band = false;
                $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
                if (($cons)&&($resu->getId() != $cons[0]->getId()) ) {
                    $band = true;
                    $idRegi = $cons[0]->getId();
                    $this->addFlash("Orange-700", "Ya existe el código del indec: '" . $cons[0]->getCodindec() . "' en: '" . $cons[0]->getDescrip() . "' !" );
                }
                $cons = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByDescrip($form->getData()->getDescrip());
                if (($cons)&&($resu->getId() != $cons[0]->getId())&&($idRegi != $cons[0]->getId())) {
                    $band = true;
                    $this->addFlash("Orange-700", "Ya existe el Estado Civil: '" . $cons[0]->getDescrip() . "' código Indec: '" . $cons[0]->getCodindec() . "' !" );
                }
                if ($band)
                    return $this->redirectToRoute("isi_persona_estadoCivil");
                // Fin controlo q no exista el código del Indec o la descripción

                $this->usrActu($form); // datos del usuario q actualiza el registro
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('isi_persona_estadoCivil');
            }
            return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivActu", "urlAction"=>$request->getUri()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "Borrado de Estado Civil");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        if (!$resu)
            $this->addFlash("Red-700", "No existe el estado civil que quiere eliminar");
        else {
            $desc = $resu->getDescrip();
            $codi = $resu->getCodindec();

            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->addFlash("Green-700", "Se eliminó '"  . $desc . " (Indec: " . $codi . ")' ");
        }
        return $this->redirectToRoute('isi_persona_estadoCivil');
    }

    public function formularioAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "Nuevo Estado Civil");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("Green-700", "Se agregó '". trim($form->getData()->getDescrip()) ."' (".$form->getData()->getCodindec().").'");
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formulario.html.twig", array("form"=>$form->createView(),"idForm"=>"", "urlAction"=>""));
    }
}
