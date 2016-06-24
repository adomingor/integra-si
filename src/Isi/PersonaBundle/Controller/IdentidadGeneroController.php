<?php

namespace Isi\PersonaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Isi\PersonaBundle\Entity\IdentGeneros;
use Isi\PersonaBundle\Form\IdentGenerosType;

class IdentidadGeneroController extends Controller
{
    private function mensajes($cual){
        switch ($cual) {
            case 0:
                $mensaje = $this->addFlash("error", "Ups! ¬ Ocurrió un error en la <i class='fa fa-database fa-lg text-muted' aria-hidden='true'></i>");
                break;
            case 1:
                $mensaje = "<br><br><p class='text-muted'><small><i class='fa fa-lightbulb-o fa-lg text-warning' aria-hidden='true'></i> Utiliza el filtro de búsqueda para verificar si un dato existe.</small></p>";
                break;
            case 2:
                $mensaje = "Buen trabajo!";
                break;
            default:
                $mensaje = "no existe este mensaje";
                break;
        }
        return($mensaje);
    }

    public function indexAction(Request $request)
    {
        // var_dump(__DIR__.'/../../');
        // echo("<br>");
        // var_dump(__DIR__);
        // echo("<br>");
        // var_dump($_SERVER['DOCUMENT_ROOT']);
        // echo("<br>");
        // var_dump($request->get('_route'));
        // echo("<br>");
        // var_dump($request->getUri());
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-list-alt fa-lg'< aria-hidden='true'></i>");
        // -> findBy es para obtener todos ordenaos por genero (no es reutilizable auqi, hay que ponerlo en el repositorio, dejo solo de muerstra)
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->findBy(array(), array('genero' => 'ASC'));
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
            $this->addFlash("warning", "duplicado ¬ ya existe el género que intenta agregar");
        }
        catch (\Exception $e) { // excepcion general
            $band = false;
            $this->addFlash("error", "Ups! ¬" . $e->getMessage());
        }
        return ($band);
    }

    public function nuevoAction(Request $request)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-plus fa-lg'< aria-hidden='true'></i>");
        $form = $this->createForm(IdentGenerosType::class, new IdentGeneros());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->grabar($form))
                $this->addFlash("success", "Se agregó '" . trim($form->getData()->getGenero()) . "'");
            return $this->redirectToRoute('isi_persona_identGenero');
        }
        return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
    }

    public function edicionAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-pencil fa-lg'< aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->find($id);
        if (!$resu){
            $this->addFlash("error", "No existe el género que quiere editar");
            return $this->redirectToRoute("isi_persona_identGenero");
        } else {
            $genero = $resu->getGenero(); // guardo solo para mostrar lo que se modifico
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
                    $this->addFlash("success", "Se modificó '" . $genero . "'");
                }
                catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $this->addFlash("warning", "Ya existe el género por el que intenta cambiar");
                }
                catch (\Exception $e) { // excepcion general
                    $band = false;
                    $this->addFlash("error", "Ups!: ".$e->getMessage());
                }

                return $this->redirectToRoute('isi_persona_identGenero');
            }
            return $this->render("IsiPersonaBundle:IdentidadGenero:formulario.html.twig", array("form"=>$form->createView()));
        }
    }

    public function borrarAction(Request $request, $id)
    {
        $request->getSession()->set("icoNombre", "<i class='fa fa-transgender-alt fa-2x'< aria-hidden='true'></i>&nbsp;<i class='fa fa-trash fa-lg'< aria-hidden='true'></i>");
        $resu = $this->getDoctrine()->getRepository("IsiPersonaBundle:IdentGeneros")->find($id);
        if (!$resu)
            $this->addFlash("error", "No existe el género que quiere eliminar");
        else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resu);
            $em->flush();
            $this->addFlash("success", "Se eliminó '" . $resu->getGenero() . "'");
        }
        return $this->redirectToRoute("isi_persona_identGenero");
    }
}
