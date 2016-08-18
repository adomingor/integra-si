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

use Symfony\Component\HttpFoundation\Response;

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

    /**
    * @ParamDecryptor(params={"id"})
    */
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
                return $this->redirectToRoute("isi_persona_C");
            }

            if (count($resu) == 0)
                $this->forward("isi_mensaje:msjFlash", array("id" => 6));
        }
        return $this->render("IsiPersonaBundle:Default:buscarPersona.html.twig", array("form"=>$form->createView(), "listado" => $resu, "totRegi" => count($resu), "tipoVista" => $form->get("chkCard")->getdata()));
    }

    /**
    * @ParamDecryptor(params={"id"})
    */
    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-pencil fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>&nbsp;<i class='fa fa-users fa-2x isi_iconoBuscarPersona' aria-hidden='true'></i>");
        try {
            $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:Dnies")->find($id);
        } catch (\Exception $e) { // $e->getMessage()
            $resu = null;
            $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <sp<n class='text-danger'>edicion persona (consultando)</span>"));
            return $this->redirectToRoute("isi_persona_C");
        }
        if (!$resu){
            $this->forward('isi_mensaje:msjFlash', array('id' => 6));
            return $this->redirectToRoute("isi_persona_C");
        } else {
            // var_dump($resu);
            $usrCrea = $resu->getUsuarioCrea(); // usuario q crea el registro
            $ipCrea = $resu->getIpCrea(); // ip del usaurio q crea el registro
            $fechaCrea = $resu->getFechaCrea(); // fecha y hora en que crea el registro
            $form = $this->createForm(DniesType::class, $resu);
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

    public function limpiarSeleccion(Request $request)
    {
        $sesion = $request->getSession();
        $sesion->remove("persSelecBD");
        $sesion->remove("cantPerSel");
        $this->forward("isi_mensaje:msjFlash", array("id" => 37));
    }

    public function seleccionEnSesion(Request $request, $ids)
    {
        $sesion = $request->getSession();
        $resul = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonaXIds($ids);
        $this->getUser()->setPerselec($ids);
        $sesion->set("persSelecBD", $resul);
        $sesion->set("cantPerSel", count($resul));
    }

    public function seleccionEnBD($ids)
    {
        // grabo en la bd las personas para el usuario logueado
        $usuario = $this->getDoctrine()->getRepository("IsiSesionBundle:Usuarios")->findOneByUsername($this->getUser()->getUsername());
        $usuario->setPerselec($ids);
        $this->getDoctrine()->getManager()->flush();
        // fin grabo en la bd las personas para el usuario logueado
    }

    public function guardaSeleccionAction(Request $request, $idsCodi)
    {
        $sesion = $request->getSession();
        // $sesion->remove("persSelec");
        // $sesion->remove("persSelecIds");
        // $sesion->set("persSelec", $id);

        if (empty($idsCodi)) {
            $this->limpiarSeleccion($request);
            $this->seleccionEnBD('');
        }
        else {
            $array = array_filter(explode( '¬', $idsCodi));
            // si hay datos en sesion, los recorro y si son direfentes ids al acutal los agrego
            // hacer rutina aqui
            foreach( array_keys( $array ) as $index=>$key ) {
                // echo $index . ':' . $key . $array[$key];
                if ( $index == 0 )
                    $ids = $this->get('nzo_url_encryptor')->decrypt($array[$key]);
                else
                    $ids = $ids . ", " . $this->get('nzo_url_encryptor')->decrypt($array[$key]);
                // last index ( $index == count( $array ) - 1 )
            }
            try {
                $this->seleccionEnSesion($request, $ids);
                $this->seleccionEnBD($ids);
                // $sesion->set("persSelecIds", $ids);
            } catch (Exception $e) {
                $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u>"));
                // $this->forward("isi_mensaje:msjFlash", array("id" => 1, "msjExtra" => "<br> <u class='text-danger'>intentando grabar selección de personas</u> <br>" . $e->getMessage()));
                // echo ($e->getMessage());
            }
        }
        return $this->redirectToRoute('isi_persona_C');
    }
}
