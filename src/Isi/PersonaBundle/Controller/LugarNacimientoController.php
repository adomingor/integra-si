<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Isi\PersonaBundle\Entity\LugarNacim;
use Isi\PersonaBundle\Form\LugarNacimType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;

class LugarNacimientoController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-hospital-o fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>");
        // -> findBy es para obtener todos ordenaos por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:LugarNacim")->findBy(array(), array('descrip' => 'ASC'));
        } catch (\Exception $e) { // $e->getMessage()
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>index lugar nacimiento</u>")); // usando un servicio
            $resu = null;
        }
        return $this->render("IsiPersonaBundle:LugarNacimiento:listado.html.twig", array("listado" => $resu, "totRegi" => count($resu)));
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
            $msjExtra = "Ya existe el lugar de nacimiento <b class='text-warning'>" . $form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
            $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
        }
        catch (\Exception $e) { // excepcion general $e->getMessage()
            $band = false;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar el lugar de nacimiento</u>"));
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-plus fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>&nbsp;<i class='fa fa-hospital-o fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>");
        $form = $this->createForm(LugarNacimType::class, new LugarNacim());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Se agregó el lugar de nacimiento <b class='text-success'>" . $form->getData()->getDescrip() . "</b>"));
            return $this->redirectToRoute('isi_persona_lugarNacim');
        }
        return $this->render("IsiPersonaBundle:LugarNacimiento:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fLugNacActu", "urlAction"=>$request->getUri()));
        // return $this->render("IsiPersonaBundle:LugarNacimiento:formulario.html.twig", array("form"=>$form->createView(),"idForm"=>"", "urlAction"=>""));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-pencil fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>&nbsp;<i class='fa fa-hospital-o fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:LugarNacim")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <sp<n class='text-danger'>edicion lugar de nacimiento (consultando)</span>"));
            return $this->redirectToRoute("isi_persona_lugarNacim");
        }
        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_persona_lugarNacim");
        } else {
            $descrip = $resu->getDescrip(); // guardo solo para mostrar lo que se modifico
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(LugarNacimType::class, $resu);
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $form->getData()->SetUsuarioCrea($usrCrea);
                    $form->getData()->SetIpCrea($ipCrea);
                    $form->getData()->SetFechaCrea($fechaCrea);
                    $this->usrActu($form); // datos del usuario q actualiza el registro
                    $this->getDoctrine()->getManager()->flush();
                    $this->forward("isi_mensaje:msjFlash", array("id" => 7, "msjExtra" => "<h3><span class='text-muted'>" . $descrip . "</span><br><i class='fa fa-exchange' aria-hidden='true'></i><br><span class='text-success'>" . trim($form->getData()->getDescrip()) . "</span></h3>"));
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $msjExtra = "Ya existe el lugar de nacimiento <b class='text-warning'>" . $form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                }
                catch (\Exception $e) { // excepcion general $e->getMessage()
                    $band = false;
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar el lugar de nacimiento</u>"));
                }
                return $this->redirectToRoute('isi_persona_lugarNacim');
            }
            return $this->render("IsiPersonaBundle:LugarNacimiento:formularioVC.html.twig", array("form"=>$form->createView(), "idForm"=>"fLugNacActu", "urlAction"=>$request->getUri()));
        }
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-trash fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>&nbsp;<i class='fa fa-hospital-o fa-2x isi_iconoLugarNacim' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:LugarNacim")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>eliminar un lugar de nacimiento (consultando)</u>"));
            return $this->redirectToRoute("isi_persona_lugarNacim");
        }
        if (!$resu)
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->forward('isi_mensaje:msjFlash', array('id' => 8, "msjExtra" => "<br> <span class='text-danger'>" . $resu->getDescrip() . "</span>"));
        }
        return $this->redirectToRoute("isi_persona_lugarNacim");
    }
}
