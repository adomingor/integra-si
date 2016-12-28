<?php

namespace Isi\PersonalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Isi\PersonalBundle\Entity\LugarTrabajoPers;
use Isi\PersonalBundle\Form\LugarTrabajoPersType;

use Isi\PersonaBundle\Entity\Personas;
use Isi\PersonaBundle\Form\PersonasType;


use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-briefcase fa-2x isi_iconoLugTrabPers' aria-hidden='true'></i>");
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
        return $this->render("IsiPersonalBundle:Default:index.html.twig", array("form"=>$form->createView(), "listado" => $resu, "totRegi" => count($resu), "tipoVista" => $form->get("chkCard")->getdata()));
    }

    public function horarioOficinasAction(Request $request, $ids)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-briefcase fa-2x isi_iconoLugTrabPers' aria-hidden='true'></i> <i class='fa fa-calendar fa-2x' aria-hidden='true'></i>");
        // var_dump($ids);
        $idsUsr = array_filter(explode( ',', $ids)); // obtengo los ids codificados, lo paso a array
        // echo("<br>");
        // var_dump($idsUsr);
        foreach ($idsUsr as &$valor) { $valor = $this->get('nzo_url_encryptor')->decrypt($valor); } // decodifico los ids
        unset($valor);
        // echo("<br>");
        // var_dump($idsUsr);
        $idsDeco = implode(",", array_unique($idsUsr)); // elimino los repetidos y convierto el array en cadena separada por comas
        // echo("<br>");
        // var_dump($idsDeco);
        $resu = $this->getDoctrine()->getManager()->getRepository("IsiPersonaBundle:Personas")->buscarPersonaXIds($idsDeco);
        // return $this->render("IsiPersonalBundle:Default:horarioOficinas.html.twig", array("listado" => $resu));
        $lugTrabPers = new LugarTrabajoPers();
        // creo el array de Personas
        foreach ($resu as $persona) {
            $per = new Personas();
            $per->setUsuarioCrea($persona["id"]);
            $per->setFts($persona["dni"] . " - " . $persona["apellido"] . ", " . $persona["nombre"]);
            if (!empty($persona["fnac"]))
                $per->setApellido($persona["fnac"]);
            else
                $per->setApellido("__/__/____");
            // $per->setSexo($persona["sexo"]) ;
            // $per->setNn($persona["nn"]);
            $per->setEmail($persona["email"]);
            $lugTrabPers->getPersonas()->add($per);
            // $per->set($persona[""]);
        }

        // var_dump($lugTrabPers);

        $form = $this->createForm(LugarTrabajoPersType::class, $lugTrabPers);
        $form->handleRequest($request);
        if ($form->isValid()) {
            // if ($this->grabar($form))
            $this->forward("isi_mensaje:msjFlash", array("id" => 5, "msjExtra" => "Grabados de mentira"));
            return $this->redirectToRoute("isi_personal_homepage");
        }
        return $this->render("IsiPersonalBundle:Default:horarioOficinas.html.twig", array("form"=>$form->createView()));
    }
}
