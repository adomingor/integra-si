<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Isi\PersonaBundle\Entity\Dnies;
use Isi\PersonaBundle\Form\DniesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;

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
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar una persona</u>"));
                    // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar una persona</u> <br>" . $e->getMessage()));
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
                return $this->redirectToRoute("isi_persona_A");
            }
        }
        return $this->render("IsiPersonaBundle:Default:formulario.html.twig", array("form"=>$form->createView()));
    }

    public function buscarPersAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-search fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>");
        $resu = null;
        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add("txtABuscar", TextType::class)
            ->add("chkCard", CheckboxType::class, array("required"=>false)) //si esta chequeado se muestra como recuadros, sino como listado
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $resu = json_decode($this->forward("isi_buscarPers:buscar", array("texto" => $form->get("txtABuscar")->getdata()))->getContent("data"), true);
        }
        return $this->render("IsiPersonaBundle:Default:buscador.html.twig", array("form"=>$form->createView(), "listado" => $resu, "totRegi" => count($resu), "tipoVista" => $form->get("chkCard")->getdata()));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-pencil fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Dnies")->findByPersonas($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <sp<n class='text-danger'>edicion persona (consultando)</span>"));
            return $this->redirectToRoute("isi_persona_C");
        }
        if (!$resu){
            $this->forward("isi_mensaje:msjFlash", array("id" => 6));
            return $this->redirectToRoute("isi_persona_C");
        } else {
            // var_dump($resu[0]->getPersonas()->getUsuarioCrea());
            $usrCrea = $resu[0]->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu[0]->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu[0]->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(DniesType::class, $resu[0]);
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $form->getData()->SetUsuarioCrea($usrCrea);
                    $form->getData()->SetIpCrea($ipCrea);
                    $form->getData()->SetFechaCrea($fechaCrea);
                    $form->getData()->personas->SetUsuarioCrea($usrCrea);
                    $form->getData()->personas->SetIpCrea($ipCrea);
                    $form->getData()->personas->SetFechaCrea($fechaCrea);
                    $this->usrActu($form); // datos del usuario q actualiza el registro
                    $this->getDoctrine()->getManager()->flush();
                    $this->forward("isi_mensaje:msjFlash", array("id" => 7));
                    $this->seleccionPersEnSesion($request, $this->getUser()->getPerselec());
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $msjExtra = "Ya existe la persona";
                    // $msjExtra = "Ya existe la persona <b class='text-warning'>" . $form->getData()->getDescrip() . "</b><br>" . json_decode($this->forward('isi_mensaje:msjJson', array('id' => 3))->getContent(), true)["descrip"];
                    $this->forward("isi_mensaje:msjFlash", array("id" => 2, "msjExtra" => $msjExtra));
                }
                catch (\Exception $e) { // excepcion general $e->getMessage()
                    $band = false;
                    $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar la persona</u>"));
                    // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando editar la persona</u><br>" . $e->getMessage()));
                }
                return $this->redirectToRoute('isi_persona_C');
            }
            return $this->render("IsiPersonaBundle:Default:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    public function seleccionPersQuitar(Request $request)
    {
        $sesion = $request->getSession();
        $sesion->remove("persSelecBD");
        $sesion->remove("cantPerSel");
        $this->forward("isi_mensaje:msjFlash", array("id" => 37));
    }

    public function seleccionPersEnSesion(Request $request, $ids)
    {
        $sesion = $request->getSession();
        $resul = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonaXIds($ids);
        $this->getUser()->setPerselec($ids); // actualizo el objeto usuario de la sesión para poder usarlo en guardaSeleccionPersAction
        $sesion->set("persSelecBD", $resul);
        $sesion->set("cantPerSel", count($resul));
    }

    public function seleccionPersEnBD($ids) // grabo en la bd las personas para el usuario logueado
    {
        $usuario = $this->getDoctrine()->getRepository("IsiSesionBundle:Usuarios")->findOneByUsername($this->getUser()->getUsername());
        $usuario->setPerselec($ids);
        $this->getDoctrine()->getManager()->flush();
    }

    public function guardaSeleccionPersAction(Request $request, $idsCodi)
    {
        if (empty($idsCodi)) {
            $this->seleccionPersQuitar($request);
            $this->seleccionPersEnBD('');
        }
        else {
            // proceso para guardar o agregar ids sin repetidos
            $arrCodi = array_filter(explode( ',', $idsCodi)); // paso a array los ids codificados, estan separados comas
            foreach ($arrCodi as &$valor) { $valor = $this->get('nzo_url_encryptor')->decrypt($valor); } // decodifico los ids
            unset($valor); // rompe la referencia con el último elemento
            $arrUnidos = array_merge($arrCodi, array_filter(explode( ',', $this->getUser()->getPerselec()))); // uno el array nuevo con el array (lo convierto primero) del usuario si lo tuviera
            $ids = implode(",", array_unique($arrUnidos)); // elimino los repetidos y convierto el array en cadena separada por comas
            // fin proceso para guardar o agregar ids sin repetidos
            try {
                $this->seleccionPersEnSesion($request, $ids);
                $this->seleccionPersEnBD($ids);
            } catch (Exception $e) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u>"));
                // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u> <br>" . $e->getMessage()));
                // echo ($e->getMessage());
            }
        }
        return $this->redirectToRoute('isi_persona_C');
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function eliminarUnaPersSeleccionAction(Request $request, $id)
    {
        $idsUsr = array_filter(explode( ',', $this->getUser()->getPerselec())); // obtengo las personas (del usuario), lo paso a array
        unset($idsUsr[array_search($id, $idsUsr)]); // busco el id, y lo quito del array
        $this->seleccionPersEnBD(implode(",", $idsUsr)); // actualizo los datos de la sesion del usuario
        foreach ($idsUsr as &$valor) { $valor = $this->get('nzo_url_encryptor')->encrypt($valor); } // codifico los ids
        return $this->redirectToRoute('isi_persona_ABMSelPers', array('idsCodi' => implode("¬", $idsUsr)));
    }
}
