<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Isi\PersonaBundle\Entity\IdentGeneros;
use Isi\PersonaBundle\Form\IdentGenerosType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;

class IdentidadGeneroController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x isi_iconoIdentGenero' aria-hidden='true'></i>");
        // -> findBy es para obtener todos ordenaos por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->findBy(array(), array('genero' => 'ASC'));
        } catch (\Exception $e) { // $e->getMessage()
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>index identidad de género</u>")); // usando un servicio
            $resu = null;
        }
        return $this->render("IsiPersonaBundle:IdentidadGenero:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
    }

    private function usrCrea($form)
    {
        $form->getData()->SetUsuariocrea($this->getUser()->getUsername()); // usuario q crea el registro
        $form->getData()->SetIpcrea(Request::createFromGlobals()->getClientIp()); // ip del usaurio q crea el registro
        $form->getData()->SetFechacrea(new \DateTime()); // fecha y hora en que crea el registro
        return ($form);
    }

    private function usrActu($form)
    {
        $form->getData()->SetUsuarioactu($this->getUser()->getUsername()); // usuario q actualiza el registro
        $form->getData()->SetIpactu(Request::createFromGlobals()->getClientIp()); // ip del usaurio q actualiza el registro
        $form->getData()->SetFechaactu(new \DateTime()); // fecha y hora en que actualiza el registro
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

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x isi_iconoIdentGenero' aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg isi_iconoIdentGenero' aria-hidden='true'></i>");
        $form = $this->createForm(IdentGenerosType::class, new IdentGeneros());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el género <b class='text-success'>" . trim($form->getData()->getGenero()) . "</b>"));
                // $this->addFlash("success", "Se agregó '" . trim($form->getData()->getGenero()) . "'");
            return $this->redirectToRoute("isi_persona_identGenero");
        }
        return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x isi_iconoIdentGenero' aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg isi_iconoIdentGenero' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>edicion identidad de género (consultando)</u>"));
            return $this->redirectToRoute("isi_persona_identGenero");
        }

        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_persona_identGenero");
        } else {
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(IdentGenerosType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $form->getData()->SetUsuarioCrea($usrCrea);
                    $form->getData()->SetIpCrea($ipCrea);
                    $form->getData()->SetFechaCrea($fechaCrea);
                    $this->usrActu($form); // datos del usuario q actualiza el registro
                    $this->getDoctrine()->getManager()->flush();
                    $this->forward("isi_mensaje:msjFlash", array("id" => 7));
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $msjExtra = "Ya existe el género <b class='text-warning'>".$form->getData()->getGenero() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                }
                catch (\Exception $e) { // excepcion general $e->getMessage()
                    $band = false;
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar una identidad de género</u>"));
                }

                return $this->redirectToRoute("isi_persona_identGenero");
            }
            return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x isi_iconoIdentGenero' aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg isi_iconoIdentGenero' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>eliminar identidad de género (consultando)</u>"));
            return $this->redirectToRoute("isi_persona_identGenero");
        }
        if (!$resu)
            $this->forward('isi_mensaje:msjFlash', array("id" => 6));
        else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($resu);
                $em->flush();
                $this->forward('isi_mensaje:msjFlash', array('id' => 8, "msjExtra" => "<br> <span class='text-danger'>" . $resu->getGenero() . "</span>"));
            } catch (\Exception $e) { // $e->getMessage()
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>borrarndo identidad de género)</u>"));
            }
        }
        return $this->redirectToRoute("isi_persona_identGenero");
    }
}
