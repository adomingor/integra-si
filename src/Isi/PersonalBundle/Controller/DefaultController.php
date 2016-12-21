<?php

namespace Isi\PersonalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

    public function oficinasAction(Request $request, $ids)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-briefcase fa-2x isi_iconoLugTrabPers' aria-hidden='true'></i> <i class='fa fa-calendar fa-2x' aria-hidden='true'></i>");
        var_dump($ids);
        $idsUsr = array_filter(explode( ',', $ids)); // obtengo las personas (del usuario), lo paso a array
        // unset($idsUsr[array_search($id, $idsUsr)]); // busco el id, y lo quito del array
        echo("<br>");
        var_dump($idsUsr);
        foreach ($idsUsr as &$valor) { $valor = $this->get('nzo_url_encryptor')->decrypt($valor); } // decodifico los ids
        echo("<br>");
        var_dump($idsUsr);
        // echo("<br> lst_resu_pers POR GET <br>");
        // var_dump($request->query->get("lst_resu_pers"));
        // echo("<br> lst_resu_pers POR POST");
        // $request->request->get("lst_resu_pers");
        // echo("<br> todos <br>");
        // var_dump($request->request->all());
        // echo("<br> request query <br>");
        // var_dump($request->query);
        // echo("<br> request <br>");
        // var_dump($request);
        return $this->render("IsiPersonalBundle:Default:oficinas.html.twig");
    }
}
