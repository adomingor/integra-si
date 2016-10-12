<?php

namespace Isi\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Isi\ConfigBundle\Entity\TiposLugarTrabajo;
use Isi\ConfigBundle\Form\TiposLugarTrabajoType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;

class TiposLugarTrabajoController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-sitemap fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:TiposLugarTrabajo")->findBy(array(), array('nivel' => 'ASC', 'descrip' => 'ASC'));
        } catch (\Exception $e) { // $e->getMessage()
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>index tipo de lugar de trabajo</u>")); // usando un servicio
            $resu = null;
        }
        return $this->render("IsiConfigBundle:TipoLugarTrab:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
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
            $msjExtra = "Ya existe el tipo de lugar <b class='text-warning'>".$form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar el tipo de lugar de trabajo</u>"));
            // $this->addFlash("error", "Ups! ¬" . $e->getMessage());
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>&nbsp;<i class='fa fa-sitemap fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>");
        $form = $this->createForm(TiposLugarTrabajoType::class, new TiposLugarTrabajo());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
            $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el tipo de lugar <b class='text-success'>" . trim($form->getData()->getDescrip()) . "</b>"));
                // $this->addFlash("success", "Se agregó '" . trim($form->getData()->getGenero()) . "'");
            return $this->redirectToRoute("isi_config_tipoLugTrab");
        }
        return $this->render("IsiConfigBundle:TipoLugarTrab:formulario.html.twig", array("form"=>$form->createView()));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-pencil fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>&nbsp;<i class='fa fa-sitemap fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:TiposLugarTrabajo")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>edicion de tipo de lugar de trabajo (consultando)</u>"));
            return $this->redirectToRoute("isi_config_tipoLugTrab");
        }

        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_config_tipoLugTrab");
        } else {
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(TiposLugarTrabajoType::class, $resu);
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
                    $msjExtra = "Ya existe el tipo de lugar <b class='text-warning'>".$form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                }
                catch (\Exception $e) { // excepcion general $e->getMessage()
                    $band = false;
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar un tipo de lugar de trabajo</u>"));
                }

                return $this->redirectToRoute("isi_config_tipoLugTrab");
            }
            return $this->render("IsiConfigBundle:TipoLugarTrab:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-trash fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>&nbsp;<i class='fa fa-sitemap fa-2x isi_iconoTipoLugTrab' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:TiposLugarTrabajo")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>eliminar un tipo de lugar de trabajo (consultando)</u>"));
            return $this->redirectToRoute("isi_config_tipoLugTrab");
        }
        if (!$resu)
            $this->forward('isi_mensaje:msjFlash', array("id" => 6));
        else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($resu);
                $em->flush();
                $this->forward('isi_mensaje:msjFlash', array('id' => 8, "msjExtra" => "<br> <span class='text-danger'>" . $resu->getDescrip() . "</span>"));
            } catch (\Exception $e) { // $e->getMessage()
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>borrarndo tipo de lugar de trabajo)</u>"));
            }
        }
        return $this->redirectToRoute("isi_config_tipoLugTrab");
    }
}
