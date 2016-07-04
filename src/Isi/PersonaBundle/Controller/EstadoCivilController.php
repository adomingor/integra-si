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
        } catch (\Exception $e) { // $e->getMessage()
            // ver integra.js (if ( $("#isi_msjFlash").length > 0 ) ) y mensajes.html.twg (<div id="isi_msjFlash" style="display:none;">) ver servicio de mensajes (bundel Admin)
            // $this->forward('isi_mensaje:msjFlash', array('id' => 1)); // usando un servicio
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>index estado civil</u>")); // usando un servicio
            $resu = null;
        }
        // $this->forward("isi_mensaje:msjFlash", array("id" => 1)); // usando un servicio
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
            } catch (\Exception $e) { // $e->getMessage()
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>consultando indec (grabar estado civil)</u>"));
                $resu = null;
            }
            if ($resu) { // control manual, el campo codindec en la bd no es unico por que si no lo saben tienen q poner 0
                $band = false;
                // $this->forward("isi_mensaje:msjFlash", array("id" => 2)); // usando un servicio
                // $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => "El código del Indec ya existe")); // usando un servicio
                $msjExtra = "Ya existe el código del indec <b class='text-warning'>" . $resu[0]->getCodindec() . "</b><br> en el estado civil <b class='text-warning'>".$resu[0]->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
            }
        } else {
            if ($form->getData()->getCodindec() < 0) {
                $band = false;
                $this->forward("isi_mensaje:msjFlash", array("id" => 4, "msjExtra" => "<br>Corrija el <span class='text-warning'>código del Indec</span>")); // usando un servicio
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
            catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) { // $e->getMessage()
                $band = false;
                $msjExtra = "Ya existe el estado civil <b class='text-warning'>".$form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
            }
            catch (\Exception $e) { // excepcion general $e->getMessage()
                $band = false;
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>intentando grabar el estado civil</u>"));
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
            if ($this->grabar($form))
                $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el estado civil <b class='text-success'>" . $form->getData()->getDescrip() . "</b>"));
            return $this->redirectToRoute("isi_persona_estadoCivil");
        }
        return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivNuevo", "urlAction"=>$request->getUri()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg isi_icono-estCivil' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>edicion estado civil (consultando)</u>"));
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
                    } catch (\Exception $e) { // $e->getMessage()
                        $resu = null;
                        $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>estado civil (buscando indec)</u>"));
                        return $this->redirectToRoute("isi_persona_estadoCivil");
                    }
                    if (($resu2)&&($resu->getId() != $resu2[0]->getId()) ) {
                        $band = true;
                        $msjExtra = "Ya existe el código del indec <b class='text-warning'>" . $resu2[0]->getCodindec() . "</b><br> en el estado civil <b class='text-warning'>".$resu2[0]->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                        $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));

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
                        $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se actualizó el estado civil <br><span class='text-primary'>" . $desc . "</span>, indec <span class='text-primary'>" . $codi . "</span><br> Por: <span class='text-success'>" . trim($form->getData()->getDescrip()) . "</span>, indec <span class='text-success'>" . $form->getData()->getCodindec() . "</span>"));
                        // $this->addFlash("success", "buen trabajo! ¬ Se modificó '" . $desc . " (" . $codi . ")'" . " por '" . trim($form->getData()->getDescrip()) . " (" . $form->getData()->getCodindec() . ")' . ");
                    }
                    catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) { // $e->getMessage()
                        $band = false;
                        $msjExtra = "Ya existe el estado civil <b class='text-warning'>".$form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                        $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                    }
                    catch (\Exception $e) { // excepcion general $e->getMessage()
                        $band = false;
                        $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>intentando editar el estado civil</u>"));
                    }
                }
                return $this->redirectToRoute("isi_persona_estadoCivil");
            }
            return $this->render("IsiPersonaBundle:EstadoCivil:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fEstCivActu", "urlAction"=>$request->getUri()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-opera fa-2x isi_icono-estCivil' aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg isi_icono-estCivil' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:EstCiviles")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>eliminar estado civil (consultando)</u>"));
            // throw new Exception("Excepción para que la intercepte ajax");
            return $this->redirectToRoute("isi_persona_estadoCivil");
        }
        if (!$resu) {
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            // throw new \Exception("Dato no encontrado"); // mensaje que se muestra si el controlador no se ejecuta por ajax
            // return $this->redirectToRoute("isi_persona_estadoCivil"); // habilitar este y comentar el anterior si no se trabaja con ajax
        } else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($resu);
                $em->flush();
                $this->forward('isi_mensaje:msjFlash', array('id' => 8));
            } catch (\Exception $e) { // $e->getMessage()
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-info'>borrando un estado civil)</u>"));
            }
        }
        return $this->redirectToRoute("isi_persona_estadoCivil");
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
                $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el estado civil <b class='text-success'>" . $form->getData()->getDescrip() . "</b>"));

        return $this->render("IsiPersonaBundle:EstadoCivil:formulario.html.twig", array("form"=>$form->createView(),"idForm"=>"", "urlAction"=>""));
    }
}
