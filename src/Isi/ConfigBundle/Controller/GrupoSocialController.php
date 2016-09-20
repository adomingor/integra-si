<?php

namespace Isi\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Isi\ConfigBundle\Entity\GrupoSociales;
use Isi\ConfigBundle\Form\GrupoSocialesType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;

class GrupoSocialController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-users fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>");
        // -> findBy es para obtener todos ordenados por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:GrupoSociales")->findBy(array(), array('grupo' => 'ASC'));
        } catch (\Exception $e) { // $e->getMessage()
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>index grupos sociales</u>")); // usando un servicio
            $resu = null;
        }
        return $this->render("IsiConfigBundle:GrupoSocial:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
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
            $msjExtra = "Ya existe el grupo social <b class='text-warning'>".$form->getData()->getGrupo() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar el grupo social</u>"));
            // $this->addFlash("error", "Ups! ¬" . $e->getMessage());
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>");
        $form = $this->createForm(GrupoSocialesType::class, new GrupoSociales());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el grupo social <b class='text-success'>" . trim($form->getData()->getGrupo()) . "</b>"));
                // $this->addFlash("success", "Se agregó '" . trim($form->getData()->getGenero()) . "'");
            return $this->redirectToRoute("isi_config_grupoSoc");
        }
        return $this->render("IsiConfigBundle:GrupoSocial:formulario.html.twig", array("form"=>$form->createView()));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-pencil fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:GrupoSociales")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>edicion grupos sociales (consultando)</u>"));
            return $this->redirectToRoute("isi_config_grupoSoc");
        }

        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_config_grupoSoc");
        } else {
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(GrupoSocialesType::class, $resu);
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
                    $msjExtra = "Ya existe el grupo social <b class='text-warning'>".$form->getData()->getGrupo() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                }
                catch (\Exception $e) { // excepcion general $e->getMessage()
                    $band = false;
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar un grupo social</u>"));
                }

                return $this->redirectToRoute("isi_config_grupoSoc");
            }
            return $this->render("IsiConfigBundle:GrupoSocial:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-trash fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoGrupoSoc' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiConfigBundle:GrupoSociales")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>eliminar un grupo social (consultando)</u>"));
            return $this->redirectToRoute("isi_config_grupoSoc");
        }
        if (!$resu)
            $this->forward('isi_mensaje:msjFlash', array("id" => 6));
        else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($resu);
                $em->flush();
                $this->forward('isi_mensaje:msjFlash', array('id' => 8, "msjExtra" => "<br> <span class='text-danger'>" . $resu->getGrupo() . "</span>"));
            } catch (\Exception $e) { // $e->getMessage()
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>borrarndo grupo social)</u>"));
            }
        }
        return $this->redirectToRoute("isi_config_grupoSoc");
    }
}
