<?php
namespace Isi\PersonaBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\EstCiviles;
use Isi\PersonaBundle\Form\EstCivilesType;

class EstadoCivilController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findAllOrdByDescrip();
        } catch (\Exception $e) {
            $this->forward('isi_mensaje:msjFlash', array('id' => 1)); // usando un servicio
            // $this->forward('IsiAdminBundle:MensajesSistema:funcionAction', array('id' => 2)); // usando un controlador
            $resu = null;
        }
        // $this->forward('isi_mensaje:msjFlash', array('id' => 0)); // usando un servicio
        /* muestra de uso de servicio con respuesta Json y formas de manejar el dato (como objeto, como array) */
        // $msj = $this->forward('isi_mensaje:msjJson', array('id' => 300));
        // $objeto = json_decode($msj->getContent());
        // var_dump($objeto);
        // echo("<br>");
        // var_dump($objeto->{"tipo"});
        // echo("<br>");
        // var_dump($objeto->{"titulo"});
        // echo("<br>");
        // var_dump($objeto->{"descrip"});
        // echo("<br>");
        // $array = json_decode($msj->getContent(), true);
        // var_dump($array);
        // echo("<br>");
        // var_dump($array["tipo"]);
        // echo("<br>");
        // var_dump($array["titulo"]);
        // echo("<br>");
        // var_dump($array["descrip"]);
        return $this->render("IsiPersonaBundle:EstadoCivil:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

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
        if ($form->getData()->getCodindec() > 0) {
            // control manual, el campo codindec en la bd no es unico por que si no lo saben tienen q poner 0
            try {
                $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
            } catch (\Exception $e) {
                $this->forward('isi_mensaje:msjFlash', array('id' => 1));
                $resu = null;
            }
            if ($resu) { // control manual, el campo codindec en la bd no es unico por que si no lo saben tienen q poner 0
                $band = false;
                // ver integra.js (if ( $("#isi_msjFlash").length > 0 ) ) y mensajes.html.twg (<div id="isi_msjFlash" style="display:none;">) ver servicio de mensajes (bundel Admin)
                $msj = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 2))->getContent(), true);
                $msj2 = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true);
                $this->addFlash($msj["tipo"], $msj["titulo"] . "¬" . $msj["descrip"] . " el código del indec <b class='text-warning'>" . $resu[0]->getCodindec() . "</b> en el estado civil <b class='text-warning'>".$resu[0]->getDescrip() . "</b>" . "<br>" . $msj2["descrip"]);
            }
        }
        else {
            if ($form->getData()->getCodindec() < 0) {
                $band = false;
                $msj = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 4))->getContent(), true);
                $this->addFlash($msj["tipo"], $msj["titulo"] . "¬" . $msj["descrip"] . "<br><br> Corrija el <u>código del Indec</u>");
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
                // nomenclatura addFlash("TipoMensaje (ver integra.js)", "Titulo ¬ Mensaje (acepta etiquetas html)"
                $msj = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 2))->getContent(), true);
                $msj2 = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true);
                $this->addFlash($msj["tipo"], $msj["titulo"] . "¬" . $msj["descrip"] . " el estado civil <b class='text-warning'>" . $form->getData()->getDescrip() . "</b><br>" . $msj2["descrip"]);
            }
            catch (\Exception $e) { // excepcion general
                $band = false;
                $this->forward('isi_mensaje:msjFlash', array('id' => 1));
            }
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg isi_icono-estCivil' aria-hidden='true'></i>");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form)) {
                $msj = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 5))->getContent(), true);
                $this->addFlash($msj["tipo"], $msj["titulo"] . "¬" . "Se agregó el estado civil <b class='text-warning'>" . $form->getData()->getDescrip() . "</b>");
            }
            return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivNuevo", "urlAction"=>$request->getUri()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg isi_icono-estCivil' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        } catch (\Exception $e) {
            $resu = null;
            $this->forward('isi_mensaje:msjFlash', array('id' => 1));
            return $this->redirectToRoute("isi_persona_estadoCivil");
        }
        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_persona_estadoCivil");
        } else {
            $desc = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
            $codi = $resu->getCodindec(); // guardo solo para mostrar lo que se modifico
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(EstCivilesType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                // controlo q no exista el código del Indec si es mayor que 0
                $band = false;
                if ($form->getData()->getCodindec() > 0) {
                    try {
                        $resu2 = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->findByCodindec($form->getData()->getCodindec());
                    } catch (\Exception $e) {
                        $resu = null;
                        $this->forward('isi_mensaje:msjFlash', array('id' => 1));
                        return $this->redirectToRoute('isi_persona_estadoCivil');
                    }
                    if (($resu2)&&($resu->getId() != $resu2[0]->getId()) ) {
                        $band = true;
                        $msj = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 2))->getContent(), true);
                        $msj2 = json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true);
                        $this->addFlash($msj["tipo"], $msj["titulo"] . "¬" . $msj["descrip"] . " el código del indec <b class='text-warning'>" . $resu2[0]->getCodindec() . "</b> en el estado civil <b class='text-warning'>".$resu2[0]->getDescrip() . "</b>" . "<br>" . $msj2["descrip"]);
                        // $this->addFlash("warning", "duplicado ¬ ya existe el código del indec: '" . $resu2[0]->getCodindec() . "' en: '".$resu2[0]->getDescrip() . "' !'");
                    }
                }
                // Fin controlo q no exista el código del Indec si es mayor que 0
                if (!$band) {
                    try {
                        // el usuario creador no se modifica
                        $form->getData()->SetUsuarioCrea($usrCrea);
                        $form->getData()->SetIpCrea($ipCrea);
                        $form->getData()->SetFechaCrea($fechaCrea);
                        $this->usrActu($form); // datos del usuario q actualiza el registro
                        $this->getDoctrine()->getManager()->flush();
                        $this->forward('isi_mensaje:msjFlash', array('id' => 7));
                        // $this->addFlash("success", "buen trabajo! ¬ Se modificó '" . $desc . " (" . $codi . ")'" . " por '" . trim($form->getData()->getDescrip()) . " (" . $form->getData()->getCodindec() . ")' . ");
                    }
                    catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                        $band = false;
                        $this->forward('isi_mensaje:msjFlash', array('id' => 8));
                    }
                    catch (\Exception $e) { // excepcion general
                        $band = false;
                        $this->forward('isi_mensaje:msjFlash', array('id' => 1));
                    }
                }
                return $this->redirectToRoute('isi_persona_estadoCivil');
            }
            return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivActu", "urlAction"=>$request->getUri()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg isi_icono-estCivil' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        } catch (\Exception $e) {
            $resu = null;
            $this->forward('isi_mensaje:msjFlash', array('id' => 1));
            return $this->redirectToRoute('isi_persona_estadoCivil');
        }
        if (!$resu)
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->forward('isi_mensaje:msjFlash', array('id' => 9));
            // $this->addFlash("success", "Se eliminó ¬ '" . $resu->getDescrip() . " (Indec: " . $resu->getCodindec() . ")' ");
        }
        return $this->redirectToRoute('isi_persona_estadoCivil');
    }

    public function formularioAction(Request $request)
    {
        // var_dump($request->get('_route'));
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x' aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg' aria-hidden='true'></i>");
        $estCivil = new EstCiviles();
        $form = $this->createForm(EstCivilesType::class, $estCivil);
        $form->handleRequest($request);
        if ($form->isValid())
            if ($this->grabar($form))
                $this->addFlash("success", $this->mensajes(2) . "¬ Se agregó '".trim($form->getData()->getDescrip())." (".$form->getData()->getCodindec().")'.");

        return $this->render("IsiPersonaBundle:EstadoCivil:formulario.html.twig", array("form"=>$form->createView(),"idForm"=>"", "urlAction"=>""));
    }
}
